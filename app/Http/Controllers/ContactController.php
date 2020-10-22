<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactStoreRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Contact::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $contacts = Auth::user()->contacts;
        return ContactResource::collection($contacts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ContactStoreRequest $request
     * @return ContactResource
     */
    public function store(ContactStoreRequest $request)
    {
        $contact = Auth::user()->contacts()->create($request->validated());
        return new ContactResource($contact);
    }

    /**
     * Display the specified resource.
     *
     * @param Contact $contact
     * @return ContactResource
     */
    public function show(Contact $contact)
    {
        $contact->load('entries');
        return new ContactResource($contact);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ContactStoreRequest $request
     * @param Contact $contact
     * @return ContactResource
     */
    public function update(ContactStoreRequest $request, Contact $contact)
    {
        $contact->fill($request->validated());
        $contact->save();
        return new ContactResource($contact);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Contact $contact
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(null, 204);
    }
}
