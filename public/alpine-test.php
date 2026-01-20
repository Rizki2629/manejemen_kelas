<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alpine.js Test</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Alpine.js Test Page</h1>
    
    <!-- Simple Counter Test -->
    <div x-data="{ count: 0 }" class="mb-6">
        <h2 class="text-xl mb-2">Counter Test:</h2>
        <button x-on:click="count++" class="bg-blue-500 text-white px-4 py-2 rounded">Count: <span x-text="count"></span></button>
    </div>
    
    <!-- Show/Hide Test -->
    <div x-data="{ show: false }" class="mb-6">
        <h2 class="text-xl mb-2">Show/Hide Test:</h2>
        <button x-on:click="show = !show" class="bg-green-500 text-white px-4 py-2 rounded mb-2">Toggle</button>
        <div x-show="show" class="p-4 bg-gray-100 border">This content toggles!</div>
    </div>
    
    <!-- Select Dropdown Test -->
    <div x-data="{ selected: '', options: ['Option 1', 'Option 2', 'Option 3'] }" class="mb-6">
        <h2 class="text-xl mb-2">Select Test:</h2>
        <select x-model="selected" class="border p-2 mb-2">
            <option value="">Choose an option</option>
            <template x-for="option in options" :key="option">
                <option :value="option" x-text="option"></option>
            </template>
        </select>
        <div>Selected: <span x-text="selected"></span></div>
    </div>
    
    <!-- AJAX-like Test -->
    <div x-data="{ 
        items: [], 
        loading: false,
        loadItems() {
            this.loading = true;
            console.log('Loading items...');
            setTimeout(() => {
                this.items = ['Item 1', 'Item 2', 'Item 3'];
                this.loading = false;
                console.log('Items loaded:', this.items);
            }, 1000);
        }
    }" class="mb-6">
        <h2 class="text-xl mb-2">AJAX-like Test:</h2>
        <button x-on:click="loadItems()" class="bg-purple-500 text-white px-4 py-2 rounded mb-2">Load Items</button>
        <div x-show="loading" class="text-blue-500">Loading...</div>
        <ul class="list-disc pl-6">
            <template x-for="item in items" :key="item">
                <li x-text="item"></li>
            </template>
        </ul>
    </div>

    <script>
        console.log('Alpine.js Test Page Script Loaded');
        document.addEventListener('alpine:init', () => {
            console.log('Alpine.js initialized successfully!');
        });
    </script>
</body>
</html>
