<?php
/**
 * PortUNA Framework (http://www.portuna.de/)
 *
 * @author    Sattler, Sean <sattler@portuna.de>
 * @copyright PortUNA Neue Medien GmbH (http://www.portuna.de)
 * @created   09.05.2023 09:29
 */

namespace Portuna;

class GitLabAPI
{
    private const GITLAB_URL = 'https://gitlab.com/api/v4/';

    private string $_baseUrl = self::GITLAB_URL;

    private string $_accessToken = '';

    private string $_branch = 'master';

    /**
     * @throws Error
     */
    public function __construct(?string $accessToken = null)
    {
        if ($accessToken)
        {
            $this->setAccessToken($accessToken);
        }
    }

    /**
     * @throws Error
     */
    public function fetchGitlab(string $path, ?string $baseUrl = null, ?string $accessToken = null): array
    {
        if ($baseUrl)
        {
            $baseUrl = $this->getBaseUrl();
        }

        if ($accessToken)
        {
            $accessToken = $this->getAccessToken();
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'PRIVATE-TOKEN: ' . $accessToken,
        ]);

        $response   = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($statusCode !== 200)
        {
            throw new Error($response);
        }

        return json_decode($response, true);
    }

    /**
     * @throws Error
     */
    public function getFile(int $respId, string $path, ?string $branch = null): array
    {
        if (!$branch)
        {
            $branch = $this->getBranch();
        }

        return $this->fetchGitlab('projects/' . $respId . '/repository/files/' . $path . '?ref=' . $branch);
    }

    /**
     * @throws Error
     */
    public function getFileWithRange(int $respId, string $path, array $range = ['start' => 1, 'end' => 2], ?string $branch = null): array
    {
        if (!$branch)
        {
            $branch = $this->getBranch();
        }

        if (empty($range['start']))
        {
            throw new Error('"start" ist nötig für ein range fetch.');
        }

        if (empty($range['end']))
        {
            throw new Error('"end" ist nötig für ein range fetch.');
        }

        return $this->fetchGitlab('projects/' . $respId . '/repository/files/' . $path . '?ref=' . $branch . '&range[start]=' . $range['start'] . '&range[end]=' . $range['end']);
    }

    /**
     * @throws Error
     */
    public function getRepo(int $respId, ?string $branch = null): array
    {
        if (!$branch)
        {
            $branch = $this->getBranch();
        }

        return $this->fetchGitlab('projects/' . $respId . '?ref=' . $branch);
    }

    public static function convertLinesToString(array $lines): string
    {
        return implode("\n", $lines);
    }

    public function getBaseUrl(): string
    {
        return $this->_baseUrl;
    }

    public function setBaseUrl(string $baseUrl): GitLabAPI
    {
        $this->_baseUrl = $baseUrl;

        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->_accessToken;
    }

    public function setAccessToken(string $accessToken): GitLabAPI
    {
        $this->_accessToken = $accessToken;

        return $this;
    }

    public function getBranch(): string
    {
        return $this->_branch;
    }

    public function setBranch(string $branch): GitLabAPI
    {
        $this->_branch = $branch;

        return $this;
    }
}
