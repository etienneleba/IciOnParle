<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EtherpadClient
{
    /** @var HttpClientInterface */
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function createGroup(): string
    {
        $content = $this->api('createGroup', [
            'padName' => 'pad',
        ]);

        return $content['data']['groupID'];
    }

    public function createGroupPad(string $groupId): string
    {
        $content = $this->api('createGroupPad', [
            'groupID' => $groupId,
            'padName' => 'pad',
        ]);

        return $content['data']['padID'];
    }

    public function deleteGroup(string $groupId)
    {
        $this->api('deleteGroup', [
            'groupID' => $groupId,
        ]);
    }

    public function getHTML(string $padId): string
    {
        $content = $this->api('getHTML', [
            'padID' => $padId,
        ]);

        return $content['data']['html'];
    }

    public function createAuthor(string $name): string
    {
        $content = $this->api('createAuthor', [
            'name' => $name,
        ]);

        return $content['data']['authorID'];
    }

    public function createSession(string $groupId, string $authorId, int $validUntil)
    {
        $content = $this->api('createSession', [
            'groupID' => $groupId,
            'authorID' => $authorId,
            'validUntil' => $validUntil,
        ]);

        return $content['data']['sessionID'];
    }

    private function api(string $endpoint, array $data): array
    {
        $keysValues = '';
        foreach ($data as $key => $value) {
            $keysValues .= '&'.$key.'='.$value;
        }

        $response = $this->client->request('GET', "http://etherpad:9001/api/1.2.14/{$endpoint}?apikey=geffefq".$keysValues);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return json_decode($response->getContent(), true);
        }

        dd($response);

        throw new HttpException($response->getStatusCode(), $response->getContent()['message']);
    }
}
