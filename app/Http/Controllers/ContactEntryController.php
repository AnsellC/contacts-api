<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactEntryStoreRequest;
use App\Http\Requests\ContactEntryUpdateRequest;
use App\Http\Resources\ContactEntryResource;
use App\Models\Contact;
use App\Models\ContactEntry;


class ContactEntryController extends Controller
{
    public function __construct()
    {
       $this->authorizeResource(ContactEntry::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ContactEntryStoreRequest $request
     * @return ContactEntryResource
     */
    public function store(ContactEntryStoreRequest $request)
    {
        $contact = Contact::findOrFail(request('contact_id'));
        $entry = $contact->entries()->create($request->validated());
        return new ContactEntryResource($entry);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ContactEntryUpdateRequest $request
     * @param ContactEntry $contactEntry
     * @return ContactEntryResource
     */
    public function update(ContactEntryUpdateRequest $request, ContactEntry $contactEntry)
    {
        $contactEntry->fill($request->validated());
        $contactEntry->save();
        return new ContactEntryResource($contactEntry);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ContactEntry $contactEntry
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(ContactEntry $contactEntry)
    {
        $contactEntry->delete();
        return response()->json(null, 204);
    }
}
