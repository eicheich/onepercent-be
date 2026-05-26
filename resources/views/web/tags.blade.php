<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnePercent - Pick Your Passions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#9261F3',
                        'primary-dark': '#7B4DE0',
                        'primary-light': '#F3EFFF',
                        'primary-gradient-start': '#B28CFF',
                        'primary-gradient-end': '#9261F3',
                    }
                }
            }
        }
    </script>
</head>

<body
    class="bg-[#F5F5F7] min-h-screen flex flex-col items-center justify-center py-12 px-4 md:px-6 relative overflow-x-hidden selection:bg-[#B28CFF]/30 selection:text-[#9261F3]">

    {{-- Dekorasi Background --}}
    <div
        class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-gradient-to-br from-[#EAE1FF] to-[#F3EFFF] rounded-full blur-3xl opacity-60 pointer-events-none mix-blend-multiply fixed">
    </div>
    <div
        class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-gradient-to-tl from-[#EAE1FF] to-[#F3EFFF] rounded-full blur-3xl opacity-60 pointer-events-none mix-blend-multiply fixed">
    </div>

    <div class="w-full max-w-2xl relative z-10 fade-in my-auto">

        {{-- Header Section --}}
        <div class="text-center mb-8 md:mb-10">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight mb-2">Pick your passions</h1>
            <p class="text-slate-500 font-medium">Choose up to <span class="text-[#9261F3] font-bold">5 topics</span>
                (max 3 custom)</p>
        </div>

        {{-- Main Card --}}
        <div
            class="bg-white rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-[#EAE1FF] p-6 md:p-10 relative overflow-hidden">
            <form method="POST" action="{{ route('web.tags.save') }}" id="tagsForm">
                @csrf

                @if ($errors->any())
                    <div
                        class="mb-6 p-4 bg-red-50 border border-red-100 text-red-500 rounded-[1.2rem] text-sm font-bold flex items-center gap-2">
                        <span>⚠️</span> {{ $errors->first() }}
                    </div>
                @endif

                @php
                    $emojis = [
                        'technology' => '💻',
                        'business' => '💼',
                        'sports' => '⚽',
                        'health' => '🏃',
                        'finance' => '💰',
                        'education' => '📚',
                        'entertainment' => '🎬',
                        'lifestyle' => '✨',
                    ];
                @endphp

                <div class="mb-8">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-4 ml-1">Popular
                        Topics</label>
                    <div class="flex flex-wrap gap-2 md:gap-3">
                        @foreach ($availableTags as $tag)
                            <button type="button" data-tag="{{ $tag }}"
                                class="tag-btn px-5 py-3 rounded-[1.2rem] border-2 border-slate-100 bg-white
                                       text-slate-500 text-sm font-bold transition-all duration-200
                                       hover:border-[#EAE1FF] hover:bg-slate-50 hover:-translate-y-0.5
                                       active:scale-95 shadow-sm">
                                <span class="mr-1">{{ $emojis[$tag] ?? '🏷' }}</span> {{ ucfirst($tag) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-wider mb-4 ml-1">
                        Add Custom Tag <span class="text-slate-300 normal-case font-semibold tracking-normal">(Optional,
                            max 3)</span>
                    </label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <span
                                class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-lg pointer-events-none">✨</span>
                            <input type="text" id="customTagInput" placeholder="e.g. photography, gaming..."
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-[1.2rem] pl-11 pr-5 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:border-[#B28CFF] focus:bg-white focus:ring-4 focus:ring-[#B28CFF]/10 transition-all placeholder:text-slate-300">
                        </div>
                        <button type="button" id="addCustomBtn"
                            class="bg-[#F3EFFF] text-[#9261F3] px-8 py-4 rounded-[1.2rem] font-black tracking-wide hover:bg-[#EAE1FF] active:scale-95 transition-all whitespace-nowrap border border-[#EAE1FF]/50">
                            Add Tag
                        </button>
                    </div>
                    <div id="customTagsContainer" class="flex flex-wrap gap-2 mt-4"></div>
                </div>

                <div id="selectedDisplay" class="mb-8 hidden p-5 bg-slate-50 rounded-[1.5rem] border border-slate-100">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-wider mb-3">
                        Selected (<span id="selectedCount" class="text-[#9261F3]">0</span>/5)
                    </p>
                    <div id="selectedChips" class="flex flex-wrap gap-2"></div>
                </div>

                <div id="hiddenInputs"></div>

                <div class="pt-2">
                    <button type="submit" id="submitBtn" disabled
                        class="w-full bg-gradient-to-r from-[#B28CFF] to-[#9261F3] text-white py-4 rounded-[1.5rem] font-black text-lg
                               shadow-lg shadow-[#9261F3]/20 hover:shadow-[#9261F3]/40 hover:-translate-y-0.5
                               transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed
                               disabled:hover:translate-y-0 disabled:hover:shadow-none flex items-center justify-center gap-2">
                        Continue
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const selectedTags = new Set()
            const customTags = []
            const MAX_TAGS = 5
            const MAX_CUSTOM = 3
            // Note: Pastikan $availableTags dikirim dari controller atau jadikan dummy array saat testing di html lokal
            const availableTags = {!! json_encode(
                $availableTags ?? [
                    'technology',
                    'business',
                    'sports',
                    'health',
                    'finance',
                    'education',
                    'entertainment',
                    'lifestyle',
                ],
            ) !!};

            // Tag button click
            document.querySelectorAll('.tag-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tag = this.dataset.tag
                    toggleTag(tag, this)
                })
            })

            // Add custom tag
            document.getElementById('addCustomBtn').addEventListener('click', addCustomTag)
            document.getElementById('customTagInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault()
                    addCustomTag()
                }
            })

            function toggleTag(tag, btn) {
                if (selectedTags.has(tag)) {
                    selectedTags.delete(tag)
                    // Revert to inactive styles
                    btn.classList.remove('border-[#B28CFF]', 'bg-[#F3EFFF]', 'text-[#9261F3]', 'shadow-inner')
                    btn.classList.add('border-slate-100', 'bg-white', 'text-slate-500', 'shadow-sm')
                } else {
                    if (selectedTags.size >= MAX_TAGS) {
                        alert('Maximum 5 tags allowed')
                        return
                    }
                    selectedTags.add(tag)
                    // Apply active styles
                    btn.classList.add('border-[#B28CFF]', 'bg-[#F3EFFF]', 'text-[#9261F3]', 'shadow-inner')
                    btn.classList.remove('border-slate-100', 'bg-white', 'text-slate-500', 'shadow-sm')
                }
                updateUI()
            }

            function addCustomTag() {
                const input = document.getElementById('customTagInput')
                const tag = input.value.trim().toLowerCase()

                if (!tag || tag.length < 2) {
                    alert('Tag must be at least 2 characters')
                    return
                }
                if (customTags.length >= MAX_CUSTOM) {
                    alert(`Maximum ${MAX_CUSTOM} custom tags`)
                    return
                }
                if (selectedTags.has(tag)) {
                    alert('Tag already selected')
                    return
                }
                if (selectedTags.size >= MAX_TAGS) {
                    alert(`Maximum ${MAX_TAGS} tags total`)
                    return
                }

                customTags.push(tag)
                selectedTags.add(tag)
                input.value = ''

                // Show custom chip
                const container = document.getElementById('customTagsContainer')
                const chip = document.createElement('span')
                chip.className =
                    'inline-flex items-center gap-2 px-4 py-2.5 bg-white border-2 border-[#B28CFF] text-[#9261F3] rounded-[1rem] text-sm font-bold shadow-sm animate-[bounce_0.3s_ease-out_1]'
                chip.dataset.customTag = tag
                chip.innerHTML = `
                    <span>🎯 ${tag}</span>
                    <button type="button" class="w-5 h-5 flex items-center justify-center bg-[#F3EFFF] rounded-full hover:bg-red-100 hover:text-red-500 transition-colors remove-custom">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                `
                chip.querySelector('.remove-custom').addEventListener('click', function() {
                    removeCustomTag(tag, chip)
                })
                container.appendChild(chip)

                updateUI()
            }

            function removeCustomTag(tag, chipEl) {
                const idx = customTags.indexOf(tag)
                if (idx > -1) customTags.splice(idx, 1)
                selectedTags.delete(tag)
                chipEl.remove()
                updateUI()
            }

            function updateUI() {
                // Update hidden inputs
                const container = document.getElementById('hiddenInputs')
                container.innerHTML = ''

                const fromAvailable = [...selectedTags].filter(t => availableTags.includes(t))
                const fromCustom = [...selectedTags].filter(t => !availableTags.includes(t))

                fromAvailable.forEach((tag, i) => {
                    const input = document.createElement('input')
                    input.type = 'hidden'
                    input.name = `tags[${i}]`
                    input.value = tag
                    container.appendChild(input)
                })

                fromCustom.forEach((tag, i) => {
                    const input = document.createElement('input')
                    input.type = 'hidden'
                    input.name = `custom_tags[${i}]`
                    input.value = tag
                    container.appendChild(input)
                })

                // Update selected display
                const display = document.getElementById('selectedDisplay')
                const chips = document.getElementById('selectedChips')
                const count = document.getElementById('selectedCount')

                count.textContent = selectedTags.size

                if (selectedTags.size > 0) {
                    display.classList.remove('hidden')
                    chips.innerHTML = [...selectedTags].map(tag =>
                        `<span class="px-3 py-1.5 bg-[#F3EFFF] border border-[#EAE1FF] text-[#9261F3] rounded-lg text-xs font-bold shadow-sm">${tag}</span>`
                    ).join('')
                } else {
                    display.classList.add('hidden')
                }

                // Submit button
                document.getElementById('submitBtn').disabled = selectedTags.size === 0
            }

        }) // end DOMContentLoaded
    </script>
</body>

</html>
