<?php

class ContactsTest extends TestCase
{
    /** @test */
    public function it_gets_list_of_contacts()
    {
        $response = $this->call('GET', 'api/contacts');
        $content = json_decode($response->getContent());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(20, count($content->data)); // Default count is 20
    }

    /** @test */
    public function it_limits_return_amount()
    {
        $response = $this->call('GET', 'api/contacts?count=5');
        $content = json_decode($response->getContent());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(5, count($content->data));
    }

    /** @test */
    public function it_includes_tags()
    {
        $response = $this->call('GET', 'api/contacts?count=5&include=tags');
        $content = json_decode($response->getContent());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty('data', $content->data[0]->tags->data);
    }

    /** @test */
    public function it_includes_tags_and_properties()
    {
        $response = $this->call('GET', 'api/contacts?count=5&include=tags,properties');
        $content = json_decode($response->getContent());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty('data', $content->data[0]->tags->data);
        $this->assertNotEmpty('data', $content->data[0]->properties->data);
    }

    /** @test */
    public function it_shows_a_contact()
    {
        $response = $this->call('GET', 'api/contacts/1');
        $content = json_decode($response->getContent());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('1', $content->id);
        $this->assertEquals('testmeister@example.com', $content->email);
    }

    /** @test */
    public function it_shows_a_contact_with_properties_and_tags()
    {
        $response = $this->call('GET', 'api/contacts/1?include=tags,properties');
        $content = json_decode($response->getContent());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('1', $content->id);
        $this->assertEquals('testmeister@example.com', $content->email);
        $this->assertNotEmpty('data', $content->tags->data);
        $this->assertNotEmpty('data', $content->properties->data);
    }

    /** @test */
    public function it_deletes_a_contact()
    {
        $response = $this->call('DELETE', 'api/contacts/1');
        $content = json_decode($response->getContent());
        $removedContact = \SevenShores\Kraken\Contact::find(1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('1', $content->id);
        $this->assertEquals('testmeister@example.com', $content->email);
        $this->assertNull($removedContact);
    }

    /** @test */
    public function it_adds_a_contact()
    {
        $data = [
            'email'  => 'testmeister10@example.com',
            'attach' => [
                'tags'  => [1, 2, 3],
                'forms' => [2],
            ],
        ];
        $response = $this->call('POST', 'api/contacts', [], [], [], $this->headers, json_encode($data));
        $content = json_decode($response->getContent());
        $addedContact = SevenShores\Kraken\Contact::where('email', $data['email'])->first();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($data['email'], $content->email);
        $this->assertEquals(3, $addedContact->tags->count());
        $this->assertEquals(2, $addedContact->forms->first()->id);
    }

    /** @test */
    public function it_updates_a_contact()
    {
        $data = [
            'email'     => 'testmeister20@example.com',
            'relations' => [
                'sync' => [
                    'tags' => [4, 5, 6]
                ],
                'attach' => [
                    'forms' => [2],
                ],
            ]
        ];
        $contactToUpdate = \SevenShores\Kraken\Contact::find(1);
        $contactToUpdate->detach('forms', [2]);
        $response = $this->call('PUT', 'api/contacts/1', [], [], [], $this->headers, json_encode($data));
        $content = json_decode($response->getContent());
        $updatedContact = \SevenShores\Kraken\Contact::find(1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($data['email'], $content->email);
        $this->assertEquals($data['email'], $updatedContact->email);
        $this->assertEquals(3, $updatedContact->tags->count());
        $this->assertEquals(2, $updatedContact->forms->where('id', 2)->first()->id);
    }
}
