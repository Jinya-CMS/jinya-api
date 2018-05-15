<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 14.05.18
 * Time: 22:00
 */

namespace App\Models\Tracker;


class Submission implements \JsonSerializable
{


    /** @var string */
    private $followUpLink;

    /**
     * Submission constructor.
     * @param string $followUpLink
     */
    public function __construct(string $followUpLink)
    {
        $this->followUpLink = $followUpLink;
    }

    /**
     * @return string
     */
    public function getFollowUpLink(): string
    {
        return $this->followUpLink;
    }

    /**
     * @param string $followUpLink
     */
    public function setFollowUpLink(string $followUpLink): void
    {
        $this->followUpLink = $followUpLink;
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
            'followUpLink' => $this->followUpLink
        ];
    }
}