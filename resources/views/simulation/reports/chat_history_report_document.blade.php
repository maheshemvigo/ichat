<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40" lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $simulation_name ?? 'Simulation' }} — Chat History</title>
    <style>
        body { font-family: Georgia, 'Times New Roman', serif; color: #1a1a1a; margin: 40px; line-height: 1.5; }
        h1 { color: #003366; font-size: 26px; border-bottom: 3px solid #003366; padding-bottom: 8px; }
        h2 { color: #00509e; font-size: 18px; margin-top: 28px; }
        h3 { color: #333; font-size: 15px; margin-top: 18px; }
        .meta { color: #555; font-size: 13px; margin-bottom: 24px; }
        .thread-block { margin-top: 28px; padding-top: 12px; border-top: 2px dashed #ddd; }
        .message-line { margin: 6px 0; font-size: 13px; }
        .speaker { font-weight: bold; color: #003366; }
        .timestamp { color: #888; font-size: 12px; }
    </style>
</head>
<body>

<h1>{{ $simulation_name ?? 'Simulation' }} — Chat History Report</h1>
<p class="meta">
    <strong>Generated:</strong> {{ $generated_at ?? date('Y-m-d H:i:s') }}<br>
    <strong>Threads:</strong> {{ count($report_data ?? []) }}
</p>

@php $simulation_name_tracker = ''; @endphp

@forelse($report_data ?? [] as $data)
    @php $i = 0; @endphp
    @foreach($data as $value)
        @php
            $simData = $value->chat_users_data ? $value->chat_users_data->simulation_data : null;
            $threadName = $value->chat_users_data ? $value->chat_users_data->name : 'Chat thread';
            $currentSimName = $simData ? $simData->simulation_name : ($simulation_name ?? '');
            $speakerName = $value->user_data ? $value->user_data->name : 'Unknown';
        @endphp

        @if($simulation_name_tracker != $currentSimName)
            <h2>{{ $currentSimName }}</h2>
        @endif

        @if($i == 0)
            <div class="thread-block">
                <h3>{{ $threadName }}</h3>
        @endif

        <p class="message-line">
            <span class="speaker">{{ $speakerName }}:</span>
            @if(!empty($value->message))
                {{ strip_tags($value->message) }}
            @elseif(!empty($value->image))
                [Image: {{ $value->upload_data ? $value->upload_data->name : 'image' }}]
            @elseif(!empty($value->video))
                [Video: {{ $value->upload_data ? $value->upload_data->name : 'video' }}]
            @elseif(!empty($value->doc))
                [Document: {{ $value->upload_data ? $value->upload_data->name : 'document' }}]
            @elseif(!empty($value->link))
                [Link: {{ $value->link }}]
            @endif
            <span class="timestamp"> — {{ $value->created_at }}</span>
        </p>

        @php
            $i++;
            $simulation_name_tracker = $currentSimName;
        @endphp
    @endforeach
    @if($i > 0)
        </div>
    @endif
@empty
    <p>No chat messages found for this simulation.</p>
@endforelse

</body>
</html>
