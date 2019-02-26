<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 25/02/2019
 * Time: 10:18
 */

namespace andreluizlunelli\BpmnRestTool\Model\Entity;

use andreluizlunelli\BpmnRestTool\System\SystemConst;
use JsonSerializable;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @package andreluizlunelli\BpmnRestTool\Model\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class User implements JsonSerializable, ToArray
{
    use EntityTrait, TimestampTrait;

    /**
     * @var string
     * @ORM\Column()
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(unique=true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    private $password;

    /**
     * User constructor.
     * @param string $name
     * @param string $email
     * @param string|null $password
     * @throws \Exception
     */
    public function __construct(string $name, string $email, ?string $password = null)
    {
        $this->createdAt = new DateTime('now');
        $this->name = $name;
        $this->email = $email;
        if ( ! empty($password))
            $this->password = self::passwordHash($password);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name
            ,'email' => $this->email
            ,'createdAt' => $this->createdAt->format(SystemConst::getDateTimeFormat())
        ];
    }

    /**
     * Não usar esse metodo para enviar informações para tela,
     * pois pode expor informações sensíveis
     *
     * @return array
     */
    public function toArray(): array
    {
        $arr = $this->jsonSerialize();
        $arr['password'] = $this->password;
        return $arr;
    }

    public static function passwordHash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function passwordVerify(string $password): bool
    {
        return password_verify($password, $this->password);
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
     * @return User
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

}