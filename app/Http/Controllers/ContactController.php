<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
public function submit(Request $request)
{
    $request->validate([
        'name' => 'required',
        'phone' => 'required',
        'email' => 'required|email',
        'website' => 'nullable'
    ]);

    $data = $request->only([
        'name',
        'phone',
        'email',
        'website'
    ]);

    Mail::to('marketing@yourdomain.com')->send(new ContactFormMail($data));

    return back()->with('success', 'Message sent successfully!');
}
}