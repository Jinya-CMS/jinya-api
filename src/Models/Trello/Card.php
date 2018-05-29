<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 15.05.18
 * Time: 15:04
 */

namespace App\Models\Trello;

class Card implements \JsonSerializable
{
    /** @var string */
    private $name;

    /** @var string */
    private $desc;

    /** @var string */
    private $idList;

    /** @var string[] */
    private $idLabels;

    private function __construct()
    {
    }

    public static function fromArray(array $data): self
    {
        $card = new self();
        $card->desc = array_key_exists('desc', $data) ? $data['desc'] : '';
        $card->name = array_key_exists('name', $data) ? $data['name'] : '';
        $card->idLabels = $data['labels'];
        $card->idList = getenv('TRELLO_LIST_ID');

        return $card;
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
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDesc(): string
    {
        return $this->desc;
    }

    /**
     * @param string $desc
     */
    public function setDesc(string $desc): void
    {
        $this->desc = $desc;
    }

    /**
     * @return string
     */
    public function getIdList(): string
    {
        return $this->idList;
    }

    /**
     * @param string $idList
     */
    public function setIdList(string $idList): void
    {
        $this->idList = $idList;
    }

    /**
     * @return string[]
     */
    public function getIdLabels(): array
    {
        return $this->idLabels;
    }

    /**
     * @param string[] $idLabels
     */
    public function setIdLabels(array $idLabels): void
    {
        $this->idLabels = $idLabels;
    }

    /**
     * Specify data which should be serialized to JSON
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'idLabels' => implode(',', $this->idLabels),
            'name' => $this->name,
            'desc' => $this->desc,
            'idList' => $this->idList,
        ];
    }
}
