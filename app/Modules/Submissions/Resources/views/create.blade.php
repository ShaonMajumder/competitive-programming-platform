@extends('layouts.app')

@section('title', $problem->title ?? 'Problem')

@section('content')
<div class="grid grid-cols-12 gap-6 min-h-[80vh]">

    <!-- Problem Statement Panel -->
    <section class="col-span-12 lg:col-span-5 bg-white rounded-md shadow-md p-6 overflow-auto max-h-[75vh]">
        <h2 class="text-2xl font-semibold mb-4">{{ $problem->title ?? 'Problem Title' }}</h2>
        <div class="prose max-w-none">
            {!! $problem->statement_html ?? '<p>No problem statement available.</p>' !!}
        </div>
    </section>

    <!-- Code Editor + Console Panel -->
    <section class="col-span-12 lg:col-span-7 flex flex-col bg-white rounded-md shadow-md overflow-hidden max-h-[75vh]">

        <!-- Submission Form -->
        <form id="submission-form" class="flex flex-col flex-grow">
            <!-- Controls -->
            <div class="flex items-center justify-between bg-gray-100 p-3 border-b border-gray-300">
                <div>
                    <label for="language" class="font-semibold mr-2">Language:</label>
                    <select name="language" id="language" class="border border-gray-300 rounded px-2 py-1">
                        <option value="cpp">C++</option>
                        <option value="python">Python</option>
                        <option value="java">Java</option>
                        <option value="php">PHP</option>
                        <option value="js">JavaScript</option>
                    </select>
                </div>
                <div class="space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded">Submit</button>
                </div>
            </div>

            <!-- Code editor content (sent via hidden input) -->
            <textarea name="code" id="code-hidden" class="hidden"></textarea>

            <!-- Monaco Editor -->
            <div id="editor" class="flex-grow border-b border-gray-300" style="height: 50vh;"></div>

            <!-- Loading Spinner -->
            <div id="loading" class="hidden bg-yellow-100 text-yellow-800 px-4 py-2 text-sm text-center">Running...</div>

            <!-- Result Output -->
            <div id="result" class="hidden bg-black text-green-400 font-mono text-sm p-4 h-32 overflow-auto whitespace-pre-wrap"></div>
        </form>
    </section>

</div>
@endsection

@push('scripts')
<script>
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.43.0/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        window.editor = monaco.editor.create(document.getElementById('editor'), {
            value: `// Write your code here\n`,
            language: 'cpp',
            theme: 'vs-dark',
            automaticLayout: true,
            fontSize: 14,
            minimap: { enabled: false }
        });

        const languageSelector = document.getElementById('language');
        languageSelector.addEventListener('change', function() {
            const langMap = {
                cpp: 'cpp',
                python: 'python',
                java: 'java',
                php: 'php',
                js: 'javascript'
            };
            monaco.editor.setModelLanguage(window.editor.getModel(), langMap[this.value]);
        });
    });
</script>

<script>
document.getElementById('submission-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const loading = document.getElementById('loading');
    const resultDiv = document.getElementById('result');
    const codeField = document.getElementById('code-hidden');

    // Sync code editor content to hidden field
    codeField.value = window.editor.getValue();

    // Hide result and show loading spinner
    resultDiv.classList.add('hidden');
    loading.classList.remove('hidden');

    // Prepare form data
    const formData = new FormData(this);

    // Send submission request via fetch API
    const response = await fetch("{{ route('submission.store') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Accept': 'application/json'
        },
        body: formData
    });

    if (!response.ok) {
        loading.classList.add('hidden');
        alert('Failed to submit code.');
        return;
    }

    const data = await response.json();
    const submissionId = data.data.id;

    // Polling function to check status every 2 seconds
    async function pollStatus() {
        const statusResponse = await fetch(`/submission/status/${submissionId}`, {
            headers: { 'Accept': 'application/json' }
        });

        if (!statusResponse.ok) {
            loading.classList.add('hidden');
            alert('Failed to fetch submission status.');
            return;
        }

        const statusData = await statusResponse.json();

        if (statusData.status === 'Queued' || statusData.status === 'Running') {
            setTimeout(pollStatus, 2000);
        } else {
            loading.classList.add('hidden');
            resultDiv.textContent = statusData.output || 'No output.';
            resultDiv.classList.remove('hidden');
        }
    }

    pollStatus();
});
</script>
@endpush
