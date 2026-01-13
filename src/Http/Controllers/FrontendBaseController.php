<?php

namespace HashtagCms\Http\Controllers;

use Illuminate\Http\Request;
use HashtagCms\Core\Main\LayoutManager;
use HashtagCms\Core\Traits\FeEssential;
use Symfony\Component\HttpFoundation\Response;

/****
 * Class FrontendBaseController
 * Renderer
 * @package HashtagCms\Http\Controllers
 */

class FrontendBaseController extends Controller
{
    use FeEssential;
    
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        // Store the request for potential use in child classes
        $this->request = $request;
        
        // Note: Child controllers should call parent::__construct($request) AFTER registering their middleware
        // This ensures that middleware registered in child controllers is properly applied
    }

    /**
     * Load data
     * InfoKeeper already has site, category, platform, and category info (Interceptor Middleware)
     */
    public function index(Request $request)
    {
        $infoLoader = app()->HashtagCms->infoLoader();
        $layoutManager = app()->HashtagCms->layoutManager();
        try {
            $data = $layoutManager->init();
            //dd($data);
            $isContentRequired = LayoutManager::getMandatoryCheck();

            info('============ Start loading data from request ============= ');
            if (isset($data['status']) && $data['status'] != 200) {
                abort($data['status'], $data['message']);
            }

            //check mandatory module
            if (isset($data['isContentFound']) && $data['isContentFound'] == false && $isContentRequired == true) {
                logger()->error('Content not found!');
                abort(Response::HTTP_NOT_FOUND, 'Content not found!');
            }

            if (isset($data['isLoginRequired']) && $data['isLoginRequired'] && auth()->user()->id == null) {
                $category = $infoLoader->getCategoryData();
                $category = $category['linkRewrite'];
                $reqParams = request()->all();

                return Redirect::to("/login?redirect=/$category?".http_build_query($reqParams, '', '&'));
            }

        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            info('Error Loading in page.');
            info($e->getMessage());
            exit($e->getMessage());
        }
        info('============ End loading data from request ============= ');

        return $layoutManager->viewMaster($layoutManager->getBaseIndex(), [], []);
    }

    /**
     * Set module mandatory check
     */
    public function setModuleMandatoryCheck(bool $value): void
    {
        LayoutManager::setMandatoryCheck($value); // this will check module mandatory false
    }
}
