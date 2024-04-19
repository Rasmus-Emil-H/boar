<?php

/**
|----------------------------------------------------------------------------
| HTML Snippets
|----------------------------------------------------------------------------
| 
| HTML Snippets for reuseability
|
| @author RE_WEB
| @package app\core\src\miscellaneous
|
*/

namespace app\core\src\miscellaneous;

class Html {

    private const PAGINATION_ADDITIONAL_PAGE_ALLOCATOR = 1;
    private const MISSING_TABLE_CONFIG_ERROR_MESSAGE = 'Frontend table configurations is missing!';

    public static function pagination(int $sqlDataQueryLength): string {

        $queryArguments = app()->getRequest()->getCompleteRequestBody()->body;
        $pageIndex = !isset($queryArguments->page) ? 0 : (int)$queryArguments->page ?? 0;

        $queryParameters = app()->getRequest()->getServerInformation()['QUERY_STRING'];
        $replacedQueryParamaters = '&' . preg_replace('/page=\d+&?/', '', $queryParameters);

        $tableConfigurations = app()->getConfig()->get('frontend')->table;
        if (!$tableConfigurations) throw new \app\core\src\exceptions\NotFoundException(self::MISSING_TABLE_CONFIG_ERROR_MESSAGE);

        $maxAllowedFrontendPages = $tableConfigurations->maximumPageInterval;

        // In case its n.(n > 0) we have to allocate an additional page
        $totalPaginationPagesNeeded = (int)($sqlDataQueryLength / $maxAllowedFrontendPages) + self::PAGINATION_ADDITIONAL_PAGE_ALLOCATOR;

        ob_start(); ?>

            <div class="card-footer border-0 p-0 mt-2">
				<nav aria-label="pagination">
					<ul class="pagination mb-0 d-flex justify-content-start">
                        <li class="page-item"><a class="page-link" href="?page=<?= ($pageIndex - 1) . $replacedQueryParamaters; ?>"><i class="fa-solid fa-chevron-left"></i></a></li>
                            <?php
                                for($i = 0; $i < $totalPaginationPagesNeeded; $i++): ?>
                                    <?php if($i > 20) continue; ?>
                                    <li class="page-item"><a class="page-link" <?= $i === $pageIndex ? 'style="color:red;font-weight:800;text-decoration:underline;"' : ''; ?> href="?page=<?= $i . $replacedQueryParamaters; ?>"><?= hs($i); ?></a></li>
                                <?php endfor;
                            ?>
						<li class="page-item"><a class="page-link" href="?page=<?= ($pageIndex + 1) . $replacedQueryParamaters; ?>"><i class="fa-solid fa-chevron-right"></i></a></li>
					</ul>
				</nav>
			</div>

		<?php return ob_get_clean();
	}

}