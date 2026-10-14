<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40" lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $payload['report']['report_title'] ?? 'Simulation Report' }}</title>
    <style>
        body { font-family: Georgia, 'Times New Roman', serif; color: #1a1a1a; margin: 40px; line-height: 1.5; }
        h1 { color: #003366; font-size: 26px; border-bottom: 3px solid #003366; padding-bottom: 8px; }
        h2 { color: #00509e; font-size: 18px; margin-top: 28px; }
        .meta { color: #555; font-size: 13px; margin-bottom: 24px; }
        .section { margin-bottom: 20px; }
        ul { margin: 8px 0 8px 20px; }
        .quote { font-style: italic; border-left: 4px solid #00509e; padding-left: 12px; margin: 10px 0; color: #444; }
        .appendix { font-size: 12px; color: #666; background: #f9f9f9; padding: 12px; border: 1px solid #eee; }
    </style>
</head>
<body>
@php
    $main = $payload['report'];
    $sections = !empty($payload['combined']) ? ($payload['sections'] ?? [$main]) : [$main];

    $renderList = function ($title, $items) {
        if (empty($items) || !is_array($items)) {
            return;
        }
        $items = array_values(array_filter($items, function ($item) {
            return trim(is_string($item) ? $item : json_encode($item)) !== '';
        }));
        if (count($items) === 0) {
            return;
        }
        echo '<div class="section"><h2>' . e($title) . '</h2><ul>';
        foreach ($items as $item) {
            echo '<li>' . e(is_string($item) ? $item : json_encode($item)) . '</li>';
        }
        echo '</ul></div>';
    };

    $renderParagraph = function ($title, $text) {
        if ($text === null || trim((string) $text) === '') {
            return;
        }
        echo '<div class="section"><h2>' . e($title) . '</h2><p>' . nl2br(e($text)) . '</p></div>';
    };
@endphp

<h1>{{ $main['report_title'] ?? 'Consolidated simulation feedback report' }}</h1>
<p class="meta">
    <strong>Simulation:</strong> {{ $main['simulation_name'] ?? '—' }}<br>
    <strong>Scope:</strong> {{ $main['thread_name'] ?? '—' }}<br>
    <strong>Generated:</strong> {{ $main['generated_at'] ?? date('Y-m-d H:i:s') }}<br>
    <strong>Records (messages) — total / analysed:</strong> {{ $main['total_record_count'] ?? '—' }} / {{ $main['analysed_record_count'] ?? '—' }}<br>
    <strong>Coverage:</strong> {{ $main['coverage_status'] ?? 'unknown' }}
</p>

@foreach($sections as $section)
    @php
        $renderParagraph('Executive Summary', $section['executive_summary'] ?? '');
        $renderParagraph('Situation Overview', $section['situation_overview'] ?? '');
        $renderList('Communication Analysis', $section['communication_analysis'] ?? []);
        $renderList('Strengths', $section['strengths'] ?? []);
        $renderList('Development Opportunities', $section['development_opportunities'] ?? []);
        $renderList('Emotional Responses and Group Dynamics', $section['emotional_responses_and_dynamics'] ?? []);
        $renderList('Ethical Considerations', $section['ethical_considerations'] ?? []);
        $renderList('Evidence Limits', $section['evidence_limits'] ?? []);
        $renderList('Classroom Concepts', $section['classroom_concepts'] ?? []);
        $renderParagraph('Overall Summary', $section['overall_summary'] ?? '');

        $renderList('Preparedness and Organisation', $section['preparedness_and_organisation'] ?? []);
        $renderList('Decision-Making Analysis', $section['decision_making_analysis'] ?? []);
        $renderList('Learning and Adaptation', $section['learning_and_adaptation'] ?? []);
        $renderList('Recommendations', $section['recommendations'] ?? []);
        $renderList('Decisions Made', $section['decisions_made'] ?? []);
        $renderList('Key Insights', $section['key_insights'] ?? []);
        $renderList('Risks and Concerns', $section['risks_and_concerns'] ?? []);

        if (!empty($section['notable_quotes']) && is_array($section['notable_quotes'])) {
            echo '<div class="section"><h2>Notable Quotes</h2>';
            foreach ($section['notable_quotes'] as $quote) {
                if (!is_array($quote)) {
                    continue;
                }
                $source = !empty($quote['source']) ? ' <em>(' . e($quote['source']) . ')</em>' : '';
                echo '<div class="quote"><strong>' . e($quote['speaker'] ?? 'Speaker') . ':</strong> "' . e($quote['quote'] ?? '') . '"' . $source . '</div>';
            }
            echo '</div>';
        }

        if (!empty($section['appendix_notes'])) {
            echo '<div class="section"><h2>Appendix Notes</h2><div class="appendix">' . nl2br(e($section['appendix_notes'])) . '</div></div>';
        }
    @endphp
@endforeach
</body>
</html>
