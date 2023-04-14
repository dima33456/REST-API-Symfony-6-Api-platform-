<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\MediaObject;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaObjectTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testCreateAMediaObject(): void
    {
        $file = new UploadedFile('./tests/imgs/test.jpg', 'image.jpg');
        $client = self::createClient();

        $client->request('POST', '/images', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIyOGIwZGI0MGJmOTBmMjgzMjYwNTczMmZmNmJlZWUzZCIsImp0aSI6Ijc5MDk2MGExMmUxM2Y1ZWFiYjRjZmU2YjgxZDM4MDhhYzMyNjkzZjI5ZjU0OGY1MDVlNjQ5NDU0NmU3NTU3ZTBkNTE0NWMwMzZmY2NhNDJkIiwiaWF0IjoxNjgxNDIyMDI4LjExODAzOSwibmJmIjoxNjgxNDIyMDI4LjExODA0MiwiZXhwIjoxNjgxNDI1NjI4LjA2MzI2LCJzdWIiOiJicnVoIiwic2NvcGVzIjpbIlJPTEVfVVNFUiJdfQ.1hxNWQXgN09J54HUGIn3F45_d8HeSu2vdO4WaKhEl-jEUpjpdot7gjkkLUczEbOWlWct1QDf6TFVSfidztnhzR-ZpePsewa0JwmDsDANqjKqWpe7SrflCEvfvLHW74qei2cFQNTOpCpaUHp3miMCIFcDi9BXOOH7QL0XuqnuLrUp6dyiOcCFIlZUatybhHyryTHDDSCoJzQARVOx-HimoBguEvsQ4NxHFXC11OWxu8gv_DNG5dsFoil1eOXdAW4MXxgHvHaHuaLczSJ6-aLWseXmTEEoxDc0gLmnwG9-RDw_3bRRDi2L1i6g8WEDaV_-H_fYYDu1THv7lyOy8gY1KQ'
            ],
            'extra' => [
                // If you have additional fields in your MediaObject entity, use the parameters.
                'parameters' => [
                    'description' => 'Test description',
                ],
                'files' => [
                    'file' => $file,
                ],
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(MediaObject::class);
        $this->assertJsonContains([
            'title' => 'My file uploaded',
        ]);
    }
}
