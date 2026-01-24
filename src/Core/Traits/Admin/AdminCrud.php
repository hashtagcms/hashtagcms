<?php

namespace HashtagCms\Core\Traits\Admin;

use HashtagCms\Core\Traits\Admin\Crud\HasCrudHelpers;
use HashtagCms\Core\Traits\Admin\Crud\HasCrudOperations;
use HashtagCms\Core\Traits\Admin\Crud\HasDataPersistence;
use HashtagCms\Core\Traits\Admin\Crud\HasRawDatabaseOps;

/**
 * Trait AdminCrud
 *
 * Main CRUD trait that composes all CRUD functionality.
 * This trait maintains backward compatibility while providing a modular architecture.
 *
 * @package HashtagCms\Core\Traits\Admin
 *
 * @see HasCrudOperations For standard CRUD methods (index, create, edit, destroy, search, publish)
 * @see HasDataPersistence For data saving operations with relationships
 * @see HasRawDatabaseOps For raw database operations
 * @see HasCrudHelpers For utility methods and getters
 */
trait AdminCrud
{
    use HasCrudOperations;
    use HasDataPersistence;
    use HasRawDatabaseOps;
    use HasCrudHelpers;

    /**
     * Store a newly created resource in storage.
     *
     * Note: This method is intentionally left commented out as it should be
     * implemented in the controller with proper validation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*public function store()
    {
        This has to be in controller because of Validator
    }*/
}
