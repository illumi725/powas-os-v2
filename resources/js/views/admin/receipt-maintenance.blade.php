<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Receipt Maintenance - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-6">Receipt Database Maintenance</h1>
            
            <!-- Analyze Button -->
            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <h2 class="text-xl font-semibold mb-3">1. Analyze Database</h2>
                <p class="text-gray-600 mb-4">Check for missing receipts and duplicates</p>
                <button onclick="runAnalyze()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Run Analysis
                </button>
            </div>

            <!-- Preview Fix Button -->
            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <h2 class="text-xl font-semibold mb-3">2. Preview Fixes (Dry-Run)</h2>
                <p class="text-gray-600 mb-4">See what would be fixed without making changes</p>
                <button onclick="runPreview()" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Preview Fixes
                </button>
            </div>

            <!-- Apply Fix Button -->
            <div class="bg-white rounded-lg shadow p-6 mb-4">
                <h2 class="text-xl font-semibold mb-3">3. Apply Fixes</h2>
                <p class="text-red-600 mb-4 font-semibold">⚠️ This will modify the database!</p>
                <button onclick="confirmApply()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Apply Fixes
                </button>
            </div>

            <!-- Output Display -->
            <div id="output" class="bg-gray-900 text-green-400 rounded-lg shadow p-6 hidden">
                <h3 class="text-lg font-semibold mb-3">Output:</h3>
                <pre id="outputContent" class="whitespace-pre-wrap font-mono text-sm overflow-x-auto"></pre>
            </div>

            <!-- Loading Indicator -->
            <div id="loading" class="hidden text-center py-4">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-gray-600">Processing...</p>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function showOutput(text) {
            document.getElementById('output').classList.remove('hidden');
            document.getElementById('outputContent').textContent = text;
        }

        function hideOutput() {
            document.getElementById('output').classList.add('hidden');
        }

        function showLoading() {
            document.getElementById('loading').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loading').classList.add('hidden');
        }

        async function runAnalyze() {
            hideOutput();
            showLoading();
            
            try {
                const response = await fetch('/admin/receipts/analyze', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                hideLoading();
                showOutput(data.output);
            } catch (error) {
                hideLoading();
                alert('Error: ' + error.message);
            }
        }

        async function runPreview() {
            hideOutput();
            showLoading();
            
            try {
                const response = await fetch('/admin/receipts/preview-fix', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                hideLoading();
                showOutput(data.output);
            } catch (error) {
                hideLoading();
                alert('Error: ' + error.message);
            }
        }

        async function confirmApply() {
            if (!confirm('⚠️ This will apply fixes to the database. Continue?')) {
                return;
            }
            
            if (!confirm('Are you absolutely sure? This will create new receipt records.')) {
                return;
            }

            hideOutput();
            showLoading();
            
            try {
                const response = await fetch('/admin/receipts/apply-fix', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ confirm: 'yes' })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                hideLoading();
                
                if (data.output) {
                    showOutput(data.output);
                } else if (data.message) {
                    showOutput(data.message);
                } else {
                    showOutput('Command executed but no output returned.');
                }
                
                if (data.success) {
                    alert('✓ Fixes applied successfully!');
                }
            } catch (error) {
                hideLoading();
                showOutput('Error: ' + error.message);
                alert('Error: ' + error.message);
            }
        }
    </script>
</body>
</html>
