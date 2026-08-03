<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Restapi\Criteria;

use Illuminate\Http\Request;

/**
 * Query parameters for listing files in the media manager. Aggregating them
 * into a DTO keeps FileManagerService::getFiles() signature stable as new
 * listing dimensions are added (sort, filters, pagination, ...).
 */
final class FileListCriteria
{
    public function __construct(
        public string $baseFolder = '/',
        public string $keyword = '',
        public string $sort = 'created',
        public string $order = 'desc',
        public int $page = 1,
        public int $perPage = 20,
        public bool $includeDirectories = false,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            baseFolder: (string) $request->input('base_folder', '/'),
            keyword: (string) $request->input('keyword', ''),
            sort: (string) $request->input('sort', 'created'),
            order: (string) $request->input('order', 'desc'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 20),
            includeDirectories: (bool) $request->input('include_directories', false),
        );
    }
}
