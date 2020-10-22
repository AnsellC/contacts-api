<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $user;


    private function login()
    {
        $this->user = User::factory()->create();
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password'
        ]);
        return $response->json('token');
    }

    private function createContact($contactName = null)
    {
        $name = $contactName ?? $this->faker->name;
        return $this->postJson('/api/contacts', [
            'name' => $name,
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_create_a_contact()
    {
        $this->login();
        $contactName = $this->faker->name;
        $response = $this->createContact($contactName);
        $response->assertStatus(201);
        $this->assertDatabaseHas('contacts', [
            'user_id' => $this->user->id,
            'name'  => $contactName,
        ]);
    }

    /**
     * @test
     */
    public function an_unathenticated_user_cannot_create_contacts()
    {
        $response = $this->createContact();
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function a_contact_can_have_an_entry()
    {
        $this->login();
        $response = $this->createContact();
        $contact = $response->json('data');

        $email = $this->faker->email;
        $phone = $this->faker->phoneNumber;
        $response = $this->postJson('/api/contact_entries', [
            'contact_id' => $contact['id'],
            'type' => 'email',
            'value' => $email
        ]);
        $response->assertStatus(201);

        $response = $this->postJson('/api/contact_entries', [
            'contact_id' => $contact['id'],
            'type' => 'phone',
            'value' => $phone
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('contact_entries', [
            'contact_id' => $contact['id'],
            'type' => 'email',
            'value' => $email
        ]);
        $this->assertDatabaseHas('contact_entries', [
            'contact_id' => $contact['id'],
            'type' => 'phone',
            'value' => $phone
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_access_only_his_contacts_index()
    {
        $me = User::factory()->create();
        $otherPerson = User::factory()->create();

        $myContact = Contact::factory()->create([
            'user_id' => $me->id,
        ]);

        Contact::factory()->create([
            'user_id' => $otherPerson->id,
        ]);

        $response = $this->actingAs($me)->getJson('/api/contacts');
        $response->assertStatus(200);
        $myContacts = $response->json('data');
        $this->assertEquals($myContact->name, $myContacts[0]['name']);
        $this->assertCount(1, $myContacts);

    }

    /**
     * @test
     */
    public function a_user_can_access_more_details_about_his_contact()
    {
        $this->withoutExceptionHandling();
        $me = User::factory()->create();

        $myContact = Contact::factory()->create([
            'user_id' => $me->id,
        ]);

        $emailEntry = ContactEntry::factory()->create([
            'contact_id' => $myContact->id,
            'type' => 'email',
            'value' => $this->faker->email,
        ]);

        $phoneEntry = ContactEntry::factory()->create([
            'contact_id' => $myContact->id,
            'type' => 'phone',
            'value' => $this->faker->phoneNumber,
        ]);

        $response = $this->actingAs($me)->getJson('/api/contacts/1');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals($myContact->name, $data['name']);
        $values = Arr::pluck($data['entries'], 'value');
        $this->assertContains($emailEntry->value, $values);
        $this->assertContains($phoneEntry->value, $values);
        $this->assertCount(2, $values);
    }

    /**
     * @test
     */
    public function a_user_can_delete_a_contact()
    {

        $me = User::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $me->id,
        ]);

        $this->assertDatabaseHas('contacts', [
            'user_id' => $me->id,
            'name' => $contact->name,
        ]);
        $response = $this->actingAs($me)->deleteJson("/api/contacts/{$contact->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('contacts', [
            'user_id' => $me->id,
            'name' => $contact->name,
        ]);
    }

    /**
     * @test
     */
    public function a_user_cannot_delete_other_users_contact()
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $other->id,
        ]);

        $response = $this->actingAs($me)->deleteJson("/api/contacts/{$contact->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('contacts', [
            'user_id' => $other->id,
            'name' => $contact->name,
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_update_a_contact()
    {
        $me = User::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $me->id,
        ]);

        $response = $this->actingAs($me)->patchJson("/api/contacts/{$contact->id}", [
            'name' => 'Test Name',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('contacts', [
            'user_id' => $me->id,
            'name' => 'Test Name',
        ]);
        $data = $response->json('data');
        $this->assertEquals('Test Name', $data['name']);
    }

    /**
     * @test
     */
    public function a_user_cannot_update_other_users_contact()
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $contact = Contact::factory()->create([
            'user_id' => $other->id,
        ]);

        $response = $this->actingAs($me)->patchJson("/api/contacts/{$contact->id}", [
            'name' => 'Test Name',
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('contacts', [
            'user_id' => $me->id,
            'name' => 'Test Name',
        ]);
  
    }
}
