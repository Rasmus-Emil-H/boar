<?php

/**
|----------------------------------------------------------------------------
| Entity query trait
|----------------------------------------------------------------------------
|
| This file is meant as a convenient way to do mundane queries and abstract 
| away some of the repetitive tasks
| 
| @author RE_WEB
| @package \app\core\src\traits\entity
|
*/

namespace app\core\src\traits\entity;

use \app\core\src\database\Entity;
use \app\core\src\database\querybuilder\QueryBuilder;
use \app\core\src\database\table\Table;
use \app\core\src\database\EntityMetaData;

use \app\core\src\exceptions\NotFoundException;

use \app\models\FileModel;
use \app\models\LanguageModel;
use \app\models\StateModel;

trait EntityQueryTrait {

    private const INVALID_ENTITY_DATA   = 'Data can not be empty';
    private const INVALID_ENTITY_STATUS = 'This entity does not have a status';
    private const FIND_OR_CREATE_NEW_DATA_ENTRY = ' was created due to a data entry';
    private const INVALID_ENTITY = 'Invalid entity';
    private const SQL_IS_NOT_NULL = 'IS NOT NULL';
    private const SQL_FETCH_MODE_FETCH = 'fetch';

    public function patchEntity(): self {
        $this->getQueryBuilder()->patch($this->data, $this->getKeyField(), $this->key())->run(self::SQL_FETCH_MODE_FETCH);
        return $this;
    }

    public function patchField(array|object $data): self {
        $data = (array)$data;

        unset($data['eg-csrf-token-label']);
        unset($data['action']);
        
        $this->getQueryBuilder()->patch($data, $this->getKeyField(), $this->key())->run(self::SQL_FETCH_MODE_FETCH);
        return $this;
    }
    
    public function createEntity() {
        $this->getQueryBuilder()->create($this->data)->run();
        $this->setKey(app()->getConnection()->getLastInsertedID());
        return $this;
    }

    public function init() {
		return $this->getQueryBuilder()->initializeNewEntity($this->data);
	}

    public function softDelete(): self {
		$this->patchField([Table::DELETED_AT_COLUMN => new \DateTime('Y-m-d H:i:s')]);
        return $this;
	}

    public function restore(): self {
	    $this->set([Table::DELETED_AT_COLUMN => null])->save();
        return $this;
	}

    public function query(): QueryBuilder {
        return (new QueryBuilder(get_called_class(), $this->getTableName(), $this->getKeyField()));
    }

    public function delete() {
        return $this->getQueryBuilder()->delete()->where([$this->getKeyField() => $this->key()])->run();
    }

    public function deleteWhere(array $where) {
        return $this->getQueryBuilder()->delete()->where($where)->run(); 
    }

     public function truncate() {
        return $this->getQueryBuilder()->truncate()->run();
    }

     public function trashed() {
        return $this->getQueryBuilder()->select()->where([Table::DELETED_AT_COLUMN => self::SQL_IS_NOT_NULL])->run();
    }

    public function getQueryBuilder(?string $table = null): QueryBuilder {
        $table ??= $this->getTableName();
        return new QueryBuilder(get_called_class(), $table, $this->getKeyField());
    }

    private function bootstrapQuery(array $fields = ['*']): QueryBuilder {
        return $this->query()->select();
    }

    public function find(string $field, string $value): Entity {
        return $this->bootstrapQuery()->where([$field => $value])->run(self::SQL_FETCH_MODE_FETCH);
    }

    public function findOne(string $field, string $value): Entity {
        return $this->bootstrapQuery()->where([$field => $value])->run(self::SQL_FETCH_MODE_FETCH);
    }
    
    public function findFirst(string $field, string $value): Entity {
        $tmp = $this->findOne($field, $value);
        $tmp->requireExistence();

        return $this->bootstrapQuery()->where([$field => $value])->orderBy($tmp->getKeyField(), 'ASC')->limit(1)->run(self::SQL_FETCH_MODE_FETCH);
    }

    public function findLast(string $field, string $value): Entity {
        $tmp = $this->findOne($field, $value);
        $tmp->requireExistence();

        return $this->bootstrapQuery()->where([$field => $value])->orderBy($tmp->getKeyField(), 'DESC')->limit(1)->run(self::SQL_FETCH_MODE_FETCH);
    }

    public function tableHasValidEntry(): ?object {
        return $this->bootstrapQuery()->limit(1)->run(self::SQL_FETCH_MODE_FETCH);
    }

    /**
     * 
     * @param string $field
     * @param string $value
     * @return [Entity]
     */

    public function findMultiple(string $field, string $value): array {
        return $this->bootstrapQuery()->where([$field => $value])->run();
    }

    public function findByMultiple(array $conditions): array {
        return $this->bootstrapQuery()->where($conditions)->run();
    }

    public function addMetaData(array $data, string $type = null): self {
        if (empty($data)) throw new \InvalidArgumentException(self::INVALID_ENTITY_DATA);

        (new EntityMetaData())
            ->set([
                Table::ENTITY_TYPE_COLUMN => $this->getTableName(), 
                Table::ENTITY_ID_COLUMN => $this->key() ?? 0,
                'Data' => json_encode($data),
                'Type' => $type ?? 'Default',
                'IP' => app()->getRequest()->getIP()
            ])
            ->save(addMetaData: false);

        return $this;
    }

    public function getTableColumns() {
        return (new QueryBuilder(get_called_class(), $this->getTableName(), $this->getKeyField()))->select()->run(); 
    }

    public function getMetaData(array $where = []): QueryBuilder {
        return (new EntityMetaData())->getQueryBuilder()->select()->where([Table::ENTITY_TYPE_COLUMN => $this->getTableName(), Table::ENTITY_ID_COLUMN => $this->key(), ...$where]);
    }

    public function searchMeta(array $where) {
        return $this->getMetaData($where);
    }

    public function setStatus(int $status): self {
        if (!(new StateModel($status))->exists()) throw new NotFoundException('Invalid state');

        $this->upsertCustomPivot('state_entity', $this->getTableName(), ['StateID' => $status, 'EntityID' => $this->key(), 'EntityType' => $this->getTableName()]);
        return $this;
    }

    public function state() {
        return $this->hasOnePolymorphic(StateModel::class, 'state_entity')->run('fetch');
    }

    public function coupleEntity(Entity $entity) {
		$entity->set([$this->getKeyField() => $this->key()]);
		$entity->init();
	}

    public function setSortOrder(int $sortOrder): self {
        $this->set([Table::SORT_ORDER_COLUMN => $sortOrder]);
        return $this;
    }

    public function patchSortOrder(int $sortOrder): self {
        $this->patchField([Table::SORT_ORDER_COLUMN => $sortOrder]);
        return $this;
    }

    public function setRelationelTableSortOrder(string $table, int $sortOrder, $additionalConditions = []): void {
        $this->getQueryBuilder($table)
            ->patch([Table::SORT_ORDER_COLUMN => $sortOrder])
            ->where($additionalConditions)
            ->run();
    }

    public function all(): array {
        return (new QueryBuilder(get_called_class(), $this->getTableName(), $this->getKeyField()))->select()->run();
    }

    public function search(array $arguments): array {
        return $this->bootstrapQuery()->where($arguments)->run();
    }

    public function findOrCreate(string $whereKey, string $whereValue, array $data = []): Entity {
        $lookup = $this->find($whereKey, $whereValue);
        if ($lookup->exists()) return $lookup;

        $cEntity = new $this();
        $cEntity->setData($data);
        $cEntity->save();

        return $cEntity;
    }

    public function complete() {
		$this->patchField([Table::COMPLETED_COLUMN => 1]);
	}

    public function add(object $arguments): ?array {
        return $this->crud($arguments);
    }

    public function edit(object $arguments): ?array {
        return $this->crud($arguments, 'edit');
    }

    public function getEntityTableFields(): self {
        $this->bootstrapQuery()->where()->limit(1)->run();
        return $this;
    }
    
    public function appendHistory(array $data): self {
        return $this->addMetaData($data, 'History');
    }

    public function history(?array $where = null): array|object {
        return $this->getMetaData()->where($where ?? ['Type' => 'History'])->run();
    }

    public function files() {
		return $this->hasManyToMany(FileModel::class, 'file_entity')->run();
	}

    public function attachToLanguage(int $languageID): void {
        $cLanguage = new LanguageModel($languageID);
        $cLanguage->requireExistence();

        $this->createCustomPivot($this->languagePivot(), ['EntityType' => $this->getTableName(), 'EntityID' => $this->key(), 'LanguageID' => $cLanguage->key()]);
    }

}