<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/retail/v2/model_service.proto

namespace Google\Cloud\Retail\V2;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Metadata associated with a create operation.
 *
 * Generated from protobuf message <code>google.cloud.retail.v2.CreateModelMetadata</code>
 */
class CreateModelMetadata extends \Google\Protobuf\Internal\Message
{
    /**
     * The resource name of the model that this create applies to.
     * Format:
     * `projects/{project_number}/locations/{location_id}/catalogs/{catalog_id}/models/{model_id}`
     *
     * Generated from protobuf field <code>string model = 1;</code>
     */
    private $model = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $model
     *           The resource name of the model that this create applies to.
     *           Format:
     *           `projects/{project_number}/locations/{location_id}/catalogs/{catalog_id}/models/{model_id}`
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Google\Cloud\Retail\V2\ModelService::initOnce();
        parent::__construct($data);
    }

    /**
     * The resource name of the model that this create applies to.
     * Format:
     * `projects/{project_number}/locations/{location_id}/catalogs/{catalog_id}/models/{model_id}`
     *
     * Generated from protobuf field <code>string model = 1;</code>
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * The resource name of the model that this create applies to.
     * Format:
     * `projects/{project_number}/locations/{location_id}/catalogs/{catalog_id}/models/{model_id}`
     *
     * Generated from protobuf field <code>string model = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setModel($var)
    {
        GPBUtil::checkString($var, True);
        $this->model = $var;

        return $this;
    }

}
