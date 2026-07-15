<?php

namespace App\Http\Controllers;

use App\Models\ManualBook;
use App\Http\Requests\StoreManualBookRequest;
use App\Http\Requests\UpdateManualBookRequest;
use Illuminate\Support\Str;

class ManualBookController extends Controller
{
    public function index()
    {
        $languageMap = [
            'id' => [
                'label' => __('messages.Indonesia'),
                'tone' => 'indonesia',
            ],
            'en' => [
                'label' => __('messages.English'),
                'tone' => 'english',
            ],
            'zh' => [
                'label' => __('messages.Chinese'),
                'tone' => 'chinese',
            ],
        ];

        $manualBooks = ManualBook::query()
            ->latest()
            ->get()
            ->map(function (ManualBook $manualBook) use ($languageMap) {
                $language = $manualBook->language ?: 'unknown';
                $documentUrl = asset('storage/document/' . ltrim($manualBook->file_name, '/'));
                $extension = Str::upper(pathinfo($manualBook->file_name, PATHINFO_EXTENSION) ?: 'PDF');

                return [
                    'id' => $manualBook->id,
                    'name' => $manualBook->name,
                    'language' => $language,
                    'language_label' => $languageMap[$language]['label'] ?? __('messages.Not specified'),
                    'language_tone' => $languageMap[$language]['tone'] ?? 'neutral',
                    'created_label' => $manualBook->created_at ? dateFormat($manualBook->created_at) : '-',
                    'document_url' => $documentUrl,
                    'file_name' => $manualBook->file_name,
                    'extension' => $extension,
                    'search_text' => Str::lower(trim($manualBook->name . ' ' . $language . ' ' . ($languageMap[$language]['label'] ?? ''))),
                ];
            });

        $languageOptions = $manualBooks
            ->pluck('language_label', 'language')
            ->filter()
            ->sort()
            ->all();
        $latestManual = $manualBooks->first();

        $summary = [
            [
                'label' => __('messages.Available manuals'),
                'value' => $manualBooks->count(),
            ],
            [
                'label' => __('messages.Languages'),
                'value' => count($languageOptions),
            ],
            [
                'label' => __('messages.Last Updated'),
                'value' => $latestManual['created_label'] ?? '-',
            ],
        ];

        return view('frontend.home.manual-book.index', [
            'manualBooks' => $manualBooks,
            'languageOptions' => $languageOptions,
            'summary' => $summary,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreManualBookRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreManualBookRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ManualBook  $manualBook
     * @return \Illuminate\Http\Response
     */
    public function show(ManualBook $manualBook)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ManualBook  $manualBook
     * @return \Illuminate\Http\Response
     */
    public function edit(ManualBook $manualBook)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateManualBookRequest  $request
     * @param  \App\Models\ManualBook  $manualBook
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateManualBookRequest $request, ManualBook $manualBook)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ManualBook  $manualBook
     * @return \Illuminate\Http\Response
     */
    public function destroy(ManualBook $manualBook)
    {
        //
    }
}
