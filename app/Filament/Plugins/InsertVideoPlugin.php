<?php

namespace App\Filament\Plugins;

use Digitonic\FilamentRichEditorTools\Support\EmbeddableVideo;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Tiptap\Core\Extension;

class InsertVideoPlugin implements RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return [];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('insertVideo')
                ->action()
                ->icon(Heroicon::OutlinedFilm),
        ];
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        return [
            Action::make('insertVideo')
                ->label('Insert Video')
                ->modalHeading('Insert Video')
                ->schema([
                    TextInput::make('url')
                        ->label('Video URL')
                        ->placeholder('https://www.youtube.com/watch?v=...')
                        ->helperText('Supports YouTube and Vimeo URLs.')
                        ->url()
                        ->required(),
                    TextInput::make('caption')
                        ->label('Caption')
                        ->placeholder('Optional video caption'),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $video = new EmbeddableVideo($data['url']);
                    $embedUrl = $video->getEmbedUrl();

                    if (blank($embedUrl)) {
                        return;
                    }

                    $caption = e($data['caption'] ?? '');
                    $captionHtml = filled($caption)
                        ? "<p><small>{$caption}</small></p>"
                        : '';

                    $html = <<<HTML
                        <div>
                            <iframe src="{$embedUrl}" width="560" height="315" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                            {$captionHtml}
                        </div>
                        HTML;

                    $component->runCommands(
                        [
                            EditorCommand::make('insertContent', arguments: [$html]),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );
                }),
        ];
    }
}
