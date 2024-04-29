<?php

namespace app\core\src\html\table;

class Header {

    private \app\core\src\Request $request; 
    private const SORT_BY = '&sortBy=';
    private const ORDER_BY = '&orderBy=';
    private string $queryParameters;
    private string $orderBy;
    private string $sortBy;
    private string $page;


    public function __construct(private array $fields) {
        $this->request = app()->getRequest();
        $this->setup();
    }

    private function setup() {
        $this->queryParameters = $this->request->getServerInformation()['QUERY_STRING'];
        $this->orderBy = $this->request->getOrderBy() ?? '';
        $this->sortBy = $this->request->getSortOrder() ?? '';
        $this->page = $this->request->getPage() ?? '';
    }

    private function determineSortOrder(): string {
        return is_int(strpos($this->queryParameters, 'DESC')) ? 'ASC' : 'DESC';
    }
    
    private function getPage(): string {
        return ($this->page !== '' ? 'page='.$this->page : '');
    }

    private function alterQueryParameters(string $field): string {
        return $this->request->checkQueryStart() . $this->getPage() . self::SORT_BY . $field . self::ORDER_BY . $this->determineSortOrder();
    }

    public function create(): string {
        ob_start(); ?>
            <thead>
                <tr>
                    <?php foreach($this->fields as $key => $field): ?>
                        <th>
                            <a class="active-menu-item" <?= $this->sortBy === $field ? 'style="color:red;"' : ''; ?> href="<?= $this->alterQueryParameters($field); ?>">
                                <?= hs($key); ?>
                                <?= $this->sortBy === $field && $this->orderBy === 'ASC' ? '<i class="fa-solid fa-arrow-up"></i>' : ''; ?>
                                <?= $this->sortBy === $field && $this->orderBy === 'DESC' ? '<i class="fa-solid fa-arrow-down"></i>' : ''; ?>
                            </a>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
		<?php return ob_get_clean();
	}
    
}