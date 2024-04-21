<?php

namespace app\core\src\html\table;

class Pagination {

    private const PAGINATION_START_PAGE = 0;
    private const PAGINATION_ADDITIONAL_PAGE_ALLOCATOR = 1;
    private const PAGINATION_ADDITIONAL_PAGE_DIVIDER = 2;
    private const MISSING_TABLE_CONFIG_ERROR_MESSAGE = 'Frontend table configurations is missing!';

    private int $pageIndex;
    private int $maxAllowedFrontendPages;
    private int $totalPaginationPagesNeeded;
    private string $replacedQueryParamaters;
    private array $pages;

    public function __construct(
        private int $sqlDataQueryLength
    ) {
        $app = app();

        $tableConfigurations = $app->getConfig()->get('frontend')->table;
        if (!$tableConfigurations) throw new \app\core\src\exceptions\NotFoundException(self::MISSING_TABLE_CONFIG_ERROR_MESSAGE);

        $queryArguments = $app->getRequest()->getCompleteRequestBody()->body;
        $this->pageIndex = !isset($queryArguments->page) ? 0 : (int)$queryArguments->page ?? 0;

        $queryParameters = $app->getRequest()->getServerInformation()['QUERY_STRING'];
        $this->replacedQueryParamaters = '&' . preg_replace('/page=\d+&?/', '', $queryParameters);

        $this->maxAllowedFrontendPages = $tableConfigurations->maximumPageInterval;

        // In case its n.(n > 0) we have to allocate an additional page
        $this->totalPaginationPagesNeeded = (int)($this->sqlDataQueryLength / $this->maxAllowedFrontendPages) + self::PAGINATION_ADDITIONAL_PAGE_ALLOCATOR;

        $this->pages = $this->calculatePages();
    }

    public function calculatePages(): array {
        $needsManyPages = $this->totalPaginationPagesNeeded > $this->maxAllowedFrontendPages;
        $pageDivision = $this->maxAllowedFrontendPages / self::PAGINATION_ADDITIONAL_PAGE_DIVIDER;
        $negativIndex = $this->pageIndex - $pageDivision;
        $positiveIndex = $this->pageIndex + $pageDivision;

        $firstVisuelPage = $needsManyPages ? $negativIndex < self::PAGINATION_START_PAGE ? self::PAGINATION_START_PAGE : $negativIndex : self::PAGINATION_START_PAGE;
        $lastVisualPage = $needsManyPages ? $positiveIndex > $this->totalPaginationPagesNeeded ? $this->totalPaginationPagesNeeded : $positiveIndex : $this->totalPaginationPagesNeeded;

        for ($page = $firstVisuelPage; $page <= $lastVisualPage; $page++) $pages[] = $page;

        return $pages;
    }

    public function create(): string {
        ob_start(); ?>
            <div class="card-footer border-0 p-0 mt-2">
				<nav aria-label="pagination">
					<ul class="pagination mb-0 d-flex justify-content-start">
                        <li class="page-item"><a class="page-link" href="?page=<?= ($this->pageIndex - 1) . $this->replacedQueryParamaters; ?>"><i class="fa-solid fa-chevron-left"></i></a></li>
                            <?php foreach($this->pages as $page): ?>
                                <li class="page-item">
                                    <a class="page-link" <?= $page === $this->pageIndex ? 'style="color:red;font-weight:800;text-decoration:underline;"' : ''; ?> href="?page=<?= $page . $this->replacedQueryParamaters; ?>">
                                        <?= hs($page); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
						<li class="page-item"><a class="page-link" href="?page=<?= ($this->pageIndex + 1) . $this->replacedQueryParamaters; ?>"><i class="fa-solid fa-chevron-right"></i></a></li>
					</ul>
				</nav>
			</div>
		<?php return ob_get_clean();
	}
    
}