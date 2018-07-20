<?php

namespace Lullabot\Mpx\DataService\Feeds;

/**
 * The SubFeed object is a dependent object that defines a secondary feed related to the parent feed configuration.
 *
 * @see https://docs.theplatform.com/help/feeds-subfeed-object
 */
class SubFeed
{
    /**
     * The parameters that are passed to a custom feed adapter for processing at runtime. For more information, see FeedConfig.adapterParameters.
     *
     * @var string
     */
    protected $adapterParameters;

    /**
     * The list of object fields (that correspond to the SubFeed.feedType) that can be retrieved in a sub feed request. Feed clients can only request fields of the sub feed that are listed in this field. For more information, see FeedConfig.availableFields.
     * To display a list of categories in an mpx Player, you must include the following category fields: fullTitle, id, label, order, parentId, and title.
     *
     * @var string[]
     */
    protected $availableFields = [];

    /**
     * Not currently used.
     *
     * @var string
     */
    protected $baseQuery;

    /**
     * The sub feed items' object type. This value must be unique within the FeedConfig.subFeedsarray. The only currently supported value is Category.
     *
     * @var string
     */
    protected $feedType;

    /**
     * The default format of the sub feed. For more information, see FeedConfig.form.
     *
     * @var string
     */
    protected $form;

    /**
     * The object titles that are included in the sub feed. For example, this can be used to limit a Category sub feed to a few specific categories. This array may contain a maximum of 2,000 titles. If this field is empty, all objects are returned.
     *
     * @var string[]
     */
    protected $limitByTitles = [];

    /**
     * The object schema version of the sub feed items. For more information, see FeedConfig.schema.
     *
     * @var string
     */
    protected $schema;

    /**
     * Returns the parameters that are passed to a custom feed adapter for processing at runtime. For more information, see FeedConfig.adapterParameters.
     *
     * @return string
     */
    public function getAdapterParameters(): ?string
    {
        return $this->adapterParameters;
    }

    /**
     * Set the parameters that are passed to a custom feed adapter for processing at runtime. For more information, see FeedConfig.adapterParameters.
     *
     * @param string $adapterParameters
     */
    public function setAdapterParameters(?string $adapterParameters)
    {
        $this->adapterParameters = $adapterParameters;
    }

    /**
     * Returns the list of object fields (that correspond to the SubFeed.feedType) that can be retrieved in a sub feed request. Feed clients can only request fields of the sub feed that are listed in this field. For more information, see FeedConfig.availableFields.
     * To display a list of categories in an mpx Player, you must include the following category fields: fullTitle, id, label, order, parentId, and title.
     *
     * @return string[]
     */
    public function getAvailableFields(): array
    {
        return $this->availableFields;
    }

    /**
     * Set the list of object fields (that correspond to the SubFeed.feedType) that can be retrieved in a sub feed request. Feed clients can only request fields of the sub feed that are listed in this field. For more information, see FeedConfig.availableFields.
     * To display a list of categories in an mpx Player, you must include the following category fields: fullTitle, id, label, order, parentId, and title.
     *
     * @param string[] $availableFields
     */
    public function setAvailableFields(array $availableFields)
    {
        $this->availableFields = $availableFields;
    }

    /**
     * Returns not currently used.
     *
     * @return string
     */
    public function getBaseQuery(): ?string
    {
        return $this->baseQuery;
    }

    /**
     * Set not currently used.
     *
     * @param string $baseQuery
     */
    public function setBaseQuery(?string $baseQuery)
    {
        $this->baseQuery = $baseQuery;
    }

    /**
     * Returns the sub feed items' object type. This value must be unique within the FeedConfig.subFeedsarray. The only currently supported value is Category.
     *
     * @return string
     */
    public function getFeedType(): ?string
    {
        return $this->feedType;
    }

    /**
     * Set the sub feed items' object type. This value must be unique within the FeedConfig.subFeedsarray. The only currently supported value is Category.
     *
     * @param string $feedType
     */
    public function setFeedType(?string $feedType)
    {
        $this->feedType = $feedType;
    }

    /**
     * Returns the default format of the sub feed. For more information, see FeedConfig.form.
     *
     * @return string
     */
    public function getForm(): ?string
    {
        return $this->form;
    }

    /**
     * Set the default format of the sub feed. For more information, see FeedConfig.form.
     *
     * @param string $form
     */
    public function setForm(?string $form)
    {
        $this->form = $form;
    }

    /**
     * Returns the object titles that are included in the sub feed. For example, this can be used to limit a Category sub feed to a few specific categories. This array may contain a maximum of 2,000 titles. If this field is empty, all objects are returned.
     *
     * @return string[]
     */
    public function getLimitByTitles(): array
    {
        return $this->limitByTitles;
    }

    /**
     * Set the object titles that are included in the sub feed. For example, this can be used to limit a Category sub feed to a few specific categories. This array may contain a maximum of 2,000 titles. If this field is empty, all objects are returned.
     *
     * @param string[] $limitByTitles
     */
    public function setLimitByTitles(array $limitByTitles)
    {
        $this->limitByTitles = $limitByTitles;
    }

    /**
     * Returns the object schema version of the sub feed items. For more information, see FeedConfig.schema.
     *
     * @return string
     */
    public function getSchema(): ?string
    {
        return $this->schema;
    }

    /**
     * Set the object schema version of the sub feed items. For more information, see FeedConfig.schema.
     *
     * @param string $schema
     */
    public function setSchema(?string $schema)
    {
        $this->schema = $schema;
    }
}
