<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use Illuminate\Http\Request;

/**
 * Pages authored under CMS Pages in the admin, exposed so the web panel can
 * render them. Only published pages are served.
 */
class CmsPageController extends Controller
{
    /** Every published page — name and slug, without the bodies. */
    public function index(Request $request)
    {
        $pages = Cms::where('cms_status', 'Publish')
            ->orderBy('cms_name')
            ->get(['cms_id', 'cms_name', 'cms_slug']);

        $response['success'] = 'success';
        $response['error'] = null;
        $response['message'] = 'successfully';
        $response['data'] = $pages;

        return response()->json($response);
    }

    /** One published page, body included. */
    public function show(Request $request, $slug)
    {
        $page = Cms::where('cms_slug', $slug)
            ->where('cms_status', 'Publish')
            ->first(['cms_id', 'cms_name', 'cms_slug', 'cms_desc']);

        if (empty($page)) {
            $response['success'] = 'failed';
            $response['error'] = 'not_found';
            $response['message'] = 'Page not found';
            $response['data'] = null;

            return response()->json($response, 404);
        }

        $response['success'] = 'success';
        $response['error'] = null;
        $response['message'] = 'successfully';
        $response['data'] = $page;

        return response()->json($response);
    }
}
