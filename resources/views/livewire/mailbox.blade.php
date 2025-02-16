<div>
    <h2>Hộp thư đến</h2>
    <ul>
        @foreach($emails as $email)
            <li>
                <strong>From:</strong> {{ $email['from'] }} <br>
                <strong>Subject:</strong> {{ $email['subject'] }} <br>
                <strong>Date:</strong> {{ $email['date'] }} <br>
                <strong>Body:</strong> {{ Str::limit($email['body'], 100) }}
            </li>
            <hr>
        @endforeach
    </ul>
</div>