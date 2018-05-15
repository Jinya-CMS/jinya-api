<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 14.05.18
 * Time: 21:48
 */

namespace App\Models\Tracker;


class Bug
{
    /** @var string */
    private $url;
    /** @var string */
    private $who;
    /** @var string */
    private $title;
    /** @var string */
    private $details;
    /** @var string */
    private $reproduce;
    /** @var int */
    private $severity;
    /** @var string */
    private $jinyaVersion;
    /** @var string */
    private $phpInfo;

    /**
     * @return string
     */
    public function getJinyaVersion(): string
    {
        return $this->jinyaVersion;
    }

    /**
     * @param string $jinyaVersion
     */
    public function setJinyaVersion(string $jinyaVersion): void
    {
        $this->jinyaVersion = $jinyaVersion;
    }

    /**
     * @return string
     */
    public function getPhpInfo(): string
    {
        return $this->phpInfo;
    }

    /**
     * @param string $phpInfo
     */
    public function setPhpInfo(string $phpInfo): void
    {
        $this->phpInfo = $phpInfo;
    }

    /**
     * Creates a bug from the given array
     *
     * @param array $array
     * @return Bug
     */
    public static function fromArray(array $array): Bug
    {
        $bug = new Bug();
        $url = parse_url($array['url']);
        $bug->url = $url['path'];
        if (array_key_exists('query', $url)) {
            $bug->url .= '?' . $url['query'];
        }
        $bug->who = $array['who'];
        $bug->title = $array['title'];
        $bug->details = $array['details'];
        $bug->reproduce = $array['reproduce'];
        $bug->severity = (int)$array['severity'];
        $bug->jinyaVersion = $array['jinyaVersion'];
        $bug->phpInfo = $array['phpInfo'];

        return $bug;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
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
     * @return string
     */
    public function getReproduce(): string
    {
        return $this->reproduce;
    }

    /**
     * @param string $reproduce
     */
    public function setReproduce(string $reproduce): void
    {
        $this->reproduce = $reproduce;
    }

    /**
     * @return int
     */
    public function getSeverity(): int
    {
        return $this->severity;
    }

    /**
     * @param int $severity
     */
    public function setSeverity(int $severity): void
    {
        $this->severity = $severity;
    }

    /**
     * Converts the bug into an array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'who' => $this->who,
            'url' => $this->url,
            'phpInfo' => $this->phpInfo,
            'details' => $this->details,
            'title' => $this->title,
            'reproduce' => $this->reproduce,
            'jinyaVersion' => $this->jinyaVersion,
            'severity' => $this->severity
        ];
    }
}