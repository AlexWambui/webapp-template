<?php

namespace Modules\ContactMessage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;
use Modules\ContactMessage\Models\ContactMessage;
use Modules\ContactMessage\Http\Resources\ContactMessageResource;
use Modules\ContactMessage\Http\Resources\ContactMessageEditPageResource;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $contact_messages = ContactMessage::query()
            ->search($request->search)
            ->resolved($request->status)
            ->latest()
            ->paginate(50);

        return inertia('app/contact/Index', [
            'contact_messages' => ContactMessageResource::collection($contact_messages),
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('marketing/contact/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone_number' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:1000'],
        ], [
            'name.required' => 'Kindly fill in your full name',
            'phone_number.required' => 'Kindly fill in your phone number',
            'message.required' => 'Kindly enter your message',
        ]);

        try {
            DB::beginTransaction();

            ContactMessage::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'message' => $request->message
            ]);

            DB::commit();

            Inertia::flash('toast', [
                'type' => "success",
                'message' => "Your callback has been requested. We will contact you shortly!"
            ]);

            return to_route('contact-messages.create');
        } catch (Exception $e) {
            DB::rollback();

            Inertia::flash('toast', [
                'type' => "error",
                'message' => "Failed to save category: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }

    public function edit(ContactMessage $contact_message)
    {
        if (!$contact_message->is_read) {
            $contact_message->update(['is_read' => true]);
        }

        return inertia('app/contact/Edit', [
            'contact_message' => new ContactMessageEditPageResource($contact_message)
        ]);
    }

    public function toggleResolved(ContactMessage $contact_message)
    {
        try {
            DB::beginTransaction();

            // Toggle the resolved status
            $contact_message->update([
                'is_resolved' => !$contact_message->is_resolved
            ]);

            DB::commit();

            $status = $contact_message->is_resolved ? 'resolved' : 'unresolved';
            
            session()->flash('toast', [
                'type' => 'success',
                'message' => "Contact message marked as {$status}!"
            ]);

            return back();
            
        } catch (Exception $e) {
            DB::rollback();

            session()->flash('toast', [
                'type' => 'error',
                'message' => "Failed to update status: {$e->getMessage()}"
            ]);

            return back();
        }
    }

    public function destroy(ContactMessage $contact_message)
    {
        try {
            DB::beginTransaction();

            $contact_message->delete();

            DB::commit();

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'Contact message deleted successfully!'
            ]);

            return to_route('contact-messages.index');
            
        } catch (Exception $e) {
            DB::rollback();

            session()->flash('toast', [
                'type' => 'error',
                'message' => "Failed to delete message: {$e->getMessage()}"
            ]);

            return back();
        }
    }
}