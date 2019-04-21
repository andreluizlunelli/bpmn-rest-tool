<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 06/03/2019
 * Time: 20:08
 */

namespace andreluizlunelli\BpmnRestTool\Model\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JsonSerializable;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class BpmnEntity
 * @package andreluizlunelli\BpmnRestTool\Model\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="bpmn")
 */
class BpmnEntity implements JsonSerializable, ToArray
{
    use EntityTrait, TimestampTrait;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bpmnList", cascade={"persist"})
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $fileInformed;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="BpmnPiece", mappedBy="bpmn", cascade={"persist"})
     */
    private $generatedPieces;

    public function __construct()
    {
        $this->createdAt = new DateTime('now');
        $this->generatedPieces = new ArrayCollection();
    }

    public function toArray(): array
    {
        $arr = $this->jsonSerialize();
        $arr['id'] = $this->id;
        return $arr;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'fileInformed' => $this->fileInformed
            ,'name' => $this->name
        ];
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return BpmnEntity
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileInformed(): string
    {
        return $this->fileInformed;
    }

    /**
     * @param string $fileInformed
     * @return BpmnEntity
     */
    public function setFileInformed(string $fileInformed): self
    {
        $this->fileInformed = $fileInformed;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return BpmnEntity
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function addBpmnPiece(BpmnPiece $piece): self
    {
        $this->generatedPieces->add($piece);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getGeneratedPieces(): Collection
    {
        return $this->generatedPieces;
    }

}