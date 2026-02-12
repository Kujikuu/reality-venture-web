<?php

namespace App\Filament\Plugins;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Support\Icons\Heroicon;
use Tiptap\Core\Extension;

class InsertImagePlugin implements RichContentPlugin
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
            RichEditorTool::make('insertImage')
                ->action()
                ->icon(Heroicon::OutlinedPhoto),
        ];
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        return [
            Action::make('insertImage')
                ->label('Insert Image')
                ->modalHeading('Insert Image')
                ->schema([
                    FileUpload::make('image')
                        ->label('Upload Image')
                        ->image()
                        ->disk('public')
                        ->directory('blog-attachments')
                        ->imageEditor()
                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'image/gif'])
                        ->maxSize(5120)
                        ->required(),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $path = $data['image'];
                    $url = asset('storage/'.$path);

                    $component->runCommands(
                        [
                            EditorCommand::make('setImage', arguments: [['src' => $url]]),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );
                }),
        ];
    }
}
