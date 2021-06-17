<?php

declare(strict_types = 1);

namespace App\Model\OpenGL;

/**
 * @Description("Query parameters for a group")
 */
class Group_Query implements \JsonSerializable
{
	/**
     * @var string|null
     */
    protected $tenantId;
    /**
     * @var int|null
     */
    protected $id;
	/**
     * @var int|null
     */
    protected $parentId;
    /**
     * @var string|null
     */
    protected $name;
	/**
     * @var string|null
     */
    protected $code;
	/**
     * @var int|null
     */
    protected $affectsGross;
	/**
     * @param string|null $tenantId
     */
	public function setTenantId(?string $tenantId) : void
    {
        $this->tenantId = $tenantId;
    }
    /**
     * @return string|null
     */
    public function getTenantId() : ?string
    {
        return $this->tenantId;
    }
    /**
     * @param int|null $id
     */
    public function setId(?int $id) : void
    {
        $this->id = $id;
    }
    /**
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }
	/**
     * @param int|null $id
     */
    public function setParentId(?int $parentId) : void
    {
        $this->parentId = $parentId;
    }
    /**
     * @return int|null
     */
    public function getParentId() : ?int
    {
        return $this->parentId;
    }
    /**
     * @param string|null $name
     */
    public function setName(?string $name) : void
    {
        $this->name = $name;
    }
    /**
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name;
    }
	/**
     * @param string|null $code
     */
    public function setCode(?string $code) : void
    {
        $this->code = $code;
    }
    /**
     * @return string|null
     */
    public function getCode() : ?string
    {
        return $this->code;
    }
	/**
     * @param int|null $affectsGross
     */
    public function setAffectsGross(?int $affectsGross) : void
    {
        $this->affectsGross = $affectsGross;
    }
    /**
     * @return int|null
     */
    public function getAffectsGross() : ?int
    {
        return $this->affectsGross;
    }
    public function jsonSerialize()
    {
        return (object) array_filter(array('tenantId'=>$this->tenantId,'id' => $this->id, 'parentId' => $this->parentId,'name' => $this->name, 'code' => $this->code, 'affectsGross' => $this->affectsGross), static function ($value) : bool {
            return $value !== null;
        });
    }
}
