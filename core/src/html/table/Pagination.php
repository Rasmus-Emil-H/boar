<?php

namespace app\core\src\html\table;

class Pagination {
    private const PAGINATION_ADDITIONAL_PAGE_ALLOCATOR = 1;
    private const PAGINATION_ADDITIONAL_PAGE_DIVIDER = 2;
    private const MISSING_TABLE_CONFIG_ERROR_MESSAGE = 'Frontend table configurations is missing!';

    public static function pagination(int $sqlDataQueryLength): string {

        $app = app();

        $tableConfigurations = $app->getConfig()->get('frontend')->table;
        if (!$tableConfigurations) throw new \app\core\src\exceptions\NotFoundException(self::MISSING_TABLE_CONFIG_ERROR_MESSAGE);

        $queryArguments = $app->getRequest()->getCompleteRequestBody()->body;
        $pageIndex = !isset($queryArguments->page) ? 0 : (int)$queryArguments->page ?? 0;

        $queryParameters = $app->getRequest()->getServerInformation()['QUERY_STRING'];
        $replacedQueryParamaters = '&' . preg_replace('/page=\d+&?/', '', $queryParameters);

        $maxAllowedFrontendPages = $tableConfigurations->maximumPageInterval;

        // In case its n.(n > 0) we have to allocate an additional page
        $totalPaginationPagesNeeded = (int)($sqlDataQueryLength / $maxAllowedFrontendPages) + self::PAGINATION_ADDITIONAL_PAGE_ALLOCATOR;

        $pages = [];

        if ($totalPaginationPagesNeeded > $maxAllowedFrontendPages) {
            $min = $pageIndex - ($maxAllowedFrontendPages / self::PAGINATION_ADDITIONAL_PAGE_DIVIDER);
            if ($min < 0) $min = 0;
            $max = $pageIndex + ($maxAllowedFrontendPages / self::PAGINATION_ADDITIONAL_PAGE_DIVIDER);
            if ($max > $totalPaginationPagesNeeded) $max = $totalPaginationPagesNeeded;
            for($page = $min; $page <= $max; $page++) $pages[] = $page;
        } else {
            $max = $pageIndex + ($maxAllowedFrontendPages/self::PAGINATION_ADDITIONAL_PAGE_DIVIDER);
            for($page = 0; $page <= $totalPaginationPagesNeeded; $page++) $pages[] = $page;
        }

        ob_start(); ?>

            <div class="card-footer border-0 p-0 mt-2">
				<nav aria-label="pagination">
					<ul class="pagination mb-0 d-flex justify-content-start">
                        <li class="page-item"><a class="page-link" href="?page=<?= ($pageIndex - 1) . $replacedQueryParamaters; ?>"><i class="fa-solid fa-chevron-left"></i></a></li>
                            <?php foreach($pages as $page): ?>
                                <li class="page-item">
                                    <a class="page-link" <?= $page === $pageIndex ? 'style="color:red;font-weight:800;text-decoration:underline;"' : ''; ?> href="?page=<?= $page . $replacedQueryParamaters; ?>">
                                        <?= hs($page); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
						<li class="page-item"><a class="page-link" href="?page=<?= ($pageIndex + 1) . $replacedQueryParamaters; ?>"><i class="fa-solid fa-chevron-right"></i></a></li>
					</ul>
				</nav>
			</div>

		<?php return ob_get_clean();
	}
}