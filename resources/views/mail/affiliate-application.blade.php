<h2>New Affiliate Application</h2>

<p><strong>Name:</strong> {{ $data['name'] }}</p>

<p><strong>Email:</strong> {{ $data['email'] }}</p>

<p><strong>Website / channel:</strong> {{ $data['site'] ?: 'Not given' }}</p>

<p><strong>How they will promote:</strong><br>
{{ $data['promotion'] ?: 'Not given' }}</p>

<hr>

<p style="color:#64748b;font-size:13px;">
    Sent from the affiliate form on {{ url('/affiliate') }}.
    Approve or decline it under General Settings &rarr; Affiliate Applications.
</p>
