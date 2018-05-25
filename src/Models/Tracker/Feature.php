<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 14.05.18
 * Time: 21:52
 */

namespace App\Models\Tracker;

class Feature
{
    /** @var string */
    private $who;

    /** @var string */
    private $title;

    /** @var string */
    private $details;

    /**
     * Creates a bug from the given array
     *
     * @param array $array
     * @return Feature
     */
    public static function fromArray(array $array): self
    {
        $bug = new self();
        $bug->who = $array['who'];
        $bug->title = $array['title'];
        $bug->details = $array['details'];

        return $bug;
    }

    /**
     * @return string
     */
    public function getWho(): string
    {
        return $this->who;
    }

    /**
     * @param string $who
     */
    public function setWho(string $who): void
    {
        $this->who = $who;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDetails(): string
    {
        return $this->details;
    }

    /**
     * @param string $details
     */
    public function setDetails(string $details): void
    {
        $this->details = $details;
    }

    /**
     * Converts the feature into an array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'who' => $this->who,
            'details' => $this->details,
            'title' => $this->title,
        ];
    }
}
