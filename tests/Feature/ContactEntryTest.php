<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactEntryTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * @test
     */
    public function a_user_can_delete_a_contact_entry()
    {
        $me = User::factory()->create();
        $myContact = Contact::factory()->create([
            'user_id' => $me->id,
        ]);

        $emailEntry = ContactEntry::factory()->create([
            'contact_id' => $myContact->id,
            'type' => 'email',
            'value' => $this->faker->email,
        ]);
        // check if data exist in DB
        $this->assertDatabaseHas('contact_entries', [
            'type' => 'email',
            'value' => $emailEntry->value,
        ]);

        $response = $this->actingAs($me)->deleteJson("/api/contact_entries/{$emailEntry->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('contact_entries', $emailEntry->toArray());
    }

    /**
     * @test
     */
    public function a_user_cannot_delete_other_users_contact_entry()
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $otherContact = Contact::factory()->create([
            'user_id' => $other->id,
        ]);

        $emailEntry = ContactEntry::factory()->create([
            'contact_id' => $otherContact->id,
            'type' => 'email',
            'value' => $this->faker->email,
        ]);
        // check if data exist in DB
        $this->assertDatabaseHas('contact_entries', [
            'type' => 'email',
            'value' => $emailEntry->value,
        ]);

        $response = $this->actingAs($me)->deleteJson("/api/contact_entries/{$emailEntry->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('contact_entries', [
            'type' => 'email',
            'value' => $emailEntry->value,
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_update_a_contact_entry()
    {
        $newEmail = $this->faker->email;
        $me = User::factory()->create();
        $myContact = Contact::factory()->create([
            'user_id' => $me->id,
        ]);

        $emailEntry = ContactEntry::factory()->create([
            'contact_id' => $myContact->id,
            'type' => 'email',
            'value' => $this->faker->email,
        ]);


        $response = $this->actingAs($me)->patchJson("/api/contact_entries/{$emailEntry->id}", [
            'value' => $newEmail,
        ]);
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals($newEmail, $data['value']);
        $this->assertNotEquals($emailEntry->value, $data['value']);
    }

    /**
     * @test
     */
    public function a_user_cannot_update_another_users_contact_entry()
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $otherContact = Contact::factory()->create([
            'user_id' => $other->id,
        ]);

        $emailEntry = ContactEntry::factory()->create([
            'contact_id' => $otherContact->id,
            'type' => 'email',
            'value' => $this->faker->email,
        ]);


        $response = $this->actingAs($me)->patchJson("/api/contact_entries/{$emailEntry->id}", [
            'value' => $this->faker->email,
        ]);
        $response->assertStatus(403);
    }
}
