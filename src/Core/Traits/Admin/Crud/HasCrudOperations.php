<?php

namespace HashtagCms\Core\Traits\Admin\Crud;

use HashtagCms\Core\Helpers\Message;
use HashtagCms\Models\QueryLogger;

/**
 * Trait HasCrudOperations
 *
 * Provides standard CRUD controller methods (index, create, edit, show, destroy, search, publish)
 *
 * @package HashtagCms\Core\Traits\Admin\Crud
 */
trait HasCrudOperations
{
    /**
     * Display a listing of the resource.
     *
     * @param mixed $more Additional data to merge with the view data
     * @return \Illuminate\Http\Response
     */
    public function index($more = null)
    {

        if (!$this->checkPolicy('read')) {
            return htcms_admin_view('common.error', Message::getReadError());
        }

        $data = $this->getSegregatedData();

        if ($more != null) {
            $data = array_merge($data, $more);
        }

        //This is in Trait
        return $this->pouplate($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->edit();
    }

    /**
     * Display the specified resource. Edit Alias
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param int $param1
     * @return \Illuminate\Http\Response
     */
    public function edit($id = 0, $param1 = 0)
    {
        if (!$this->checkPolicy('edit')) {
            return htcms_admin_view('common.error', Message::getWriteError());
        }

        //Check if has pre edit
        if (method_exists($this, 'preEdit')) {
            $this->preEdit();
        }

        $dataSource = $this->getDataSource();
        $dataWith = $this->getDataWith();

        $data['results'] = [];
        $data['actionPerformed'] = ($id > 0) ? 'edit' : 'add';
        $data['backURL'] = $this->getBackURL(false, $id);

        if ($id > 0) {
            $data['results'] = $dataSource::getById($id, $dataWith, $param1);
            $data['backURL'] = $this->getBackURL(true, $id);
        }

        $data['user_rights'] = $this->getUserRights();

        //In case if you want any extra;
        $extraData = $this->getExtraDataForEdit();

        if ($extraData != null) {
            $data = array_merge($data, $extraData);
        }
        /*        
        $controller_name = request()->module_info->controller_name;
        $controller_view = request()->module_info->edit_view_name;
        $editView = ($controller_view == null || empty($controller_view)) ? $controller_name . '.addedit' : '.' . $controller_view;
        $editView = str_replace('/', '.', $editView);
        */
        $viewName = $this->getViewNames(request()->module_info, 'edit');

        return htcms_admin_view($viewName, $data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return string JSON response
     */
    public function destroy($id)
    {
        if (!$this->checkPolicy('delete')) {
            return json_encode(['id' => $id, 'success' => 0, 'message' => Message::getDeleteError()]);
        }

        QueryLogger::enableQueryLog();

        $dataSource = $this->getDataSource();
        $source = new $dataSource();

        $isDeleted = $source->destroy($id);
        $array = ['id' => $id, 'success' => $isDeleted, 'source' => $source];

        //Logging
        try {
            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log('delete', $queryLog, $source, $id ?? 0);
        } catch (\Exception $exception) {
            info($exception->getMessage());
        }

        return json_encode($array);
    }

    /**
     * Search the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        if (!$this->checkPolicy('read')) {
            return htcms_admin_view('common.error', Message::getReadError());
        }

        $data = $this->getSegregatedData();
        $collection = collect(request()->all());
        $filtered = $collection->except(['page']);
        $data['searchId'] = $filtered->all();

        //This is in Trait
        return $this->searchData($data);
    }

    /**
     * Toggle publish status of a resource
     *
     * @param int $id
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function publish($id = 0, $status = 0)
    {
        if (!$this->checkPolicy('publish')) {
            return response()->json(Message::getWriteError(), 400);
        }

        QueryLogger::enableQueryLog();

        $status = ($status == 0) ? 1 : 0;
        $where = $id;
        $saveData['publish_status'] = $status;

        $arrSaveData = ['model' => $this->dataSource, 'data' => $saveData];
        $savedData = $this->saveData($arrSaveData, $where);

        $rData = [
            'id' => $id,
            'status' => $status,
            'meta' => $savedData,
        ];

        //Logging
        try {
            $queryLog = QueryLogger::getQueryLog();
            QueryLogger::log('publish', $queryLog, $rData, $where);
        } catch (\Exception $exception) {
            info($exception->getMessage());
        }

        return response()->json($rData);
    }
}
