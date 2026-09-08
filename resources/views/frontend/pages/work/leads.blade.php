<h2>Leads List</h2>

<table border="1" cellpadding="10">
    <tr>
      
        <th>Company</th>
        <th>Person Name</th>
        <th>Title</th>
        <th>Email</th>
        <th>Phone</th>
        <th>City</th>
        
        <th>Industry</th>
    </tr>

    @foreach($leads as $lead)
    <tr>
        <td>{{ $lead['company'] }}</td>
        <td>{{ $lead['person_name'] }}</td>
        <td>{{ $lead['title'] }}</td>
        <td>{{ $lead['email'] }}</td>
        <td>{{ $lead['phone'] }}</td>
        <td>{{ $lead['city'] }}</td>
        
        <td>{{ $lead['industry'] }}</td>
    </tr>
    @endforeach
</table>