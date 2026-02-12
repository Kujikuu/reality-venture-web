<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\PostStatus;
use App\Filament\Plugins\InsertImagePlugin;
use App\Filament\Plugins\InsertVideoPlugin;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Content')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('English Content')
                            ->icon('heroicon-o-language')
                            ->schema([
                                TextInput::make('title_en')
                                    ->label('Title (English)')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if (blank($get('slug'))) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->placeholder('Enter post title in English'),
                                Textarea::make('excerpt_en')
                                    ->label('Excerpt (English)')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->placeholder('Brief summary of the post'),
                                RichEditor::make('content_en')
                                    ->label('Content (English)')
                                    ->required()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('blog-attachments')
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike', 'link'],
                                        ['h2', 'h3', 'lead', 'small'],
                                        ['alignStart', 'alignCenter', 'alignEnd'],
                                        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                        ['table', 'grid', 'horizontalRule'],
                                        ['insertImage', 'insertVideo'],
                                        ['undo', 'redo', 'clearFormatting'],
                                    ])
                                    ->plugins([
                                        InsertImagePlugin::make(),
                                        InsertVideoPlugin::make(),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Arabic Content')
                            ->icon('heroicon-o-language')
                            ->schema([
                                TextInput::make('title_ar')
                                    ->label('Title (Arabic)')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('ادخل عنوان المقالة بالعربية'),
                                Textarea::make('excerpt_ar')
                                    ->label('Excerpt (Arabic)')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->placeholder('ملخص قصير عن المقالة'),
                                RichEditor::make('content_ar')
                                    ->label('Content (Arabic)')
                                    ->required()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('blog-attachments')
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike', 'link'],
                                        ['h2', 'h3', 'lead', 'small'],
                                        ['alignStart', 'alignCenter', 'alignEnd'],
                                        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                        ['table', 'grid', 'horizontalRule'],
                                        ['insertImage', 'insertVideo'],
                                        ['undo', 'redo', 'clearFormatting'],
                                    ])
                                    ->plugins([
                                        InsertImagePlugin::make(),
                                        InsertVideoPlugin::make(),
                                    ])
                                    ->extraInputAttributes(['dir' => 'rtl'])
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Section::make('Post Settings')
                    ->description('Configure post metadata and publishing options.')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->columns(2)
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-link')
                            ->placeholder('auto-generated-from-title'),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name_en')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name_en')->label('Name (English)')->required(),
                                TextInput::make('name_ar')->label('Name (Arabic)')->required(),
                                TextInput::make('slug')->required()->unique('categories', 'slug'),
                            ])
                            ->nullable(),
                        Select::make('tags')
                            ->relationship('tags', 'name_en')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name_en')->label('Name (English)')->required(),
                                TextInput::make('name_ar')->label('Name (Arabic)')->required(),
                                TextInput::make('slug')->required()->unique('tags', 'slug'),
                            ]),
                        Select::make('user_id')
                            ->label('Author')
                            ->relationship('author', 'name')
                            ->default(fn () => auth()->id())
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('status')
                            ->options(
                                collect(PostStatus::cases())
                                    ->mapWithKeys(fn ($s) => [$s->value => $s->label()])
                            )
                            ->required()
                            ->native(false)
                            ->default('draft'),
                        DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->nullable()
                            ->prefixIcon('heroicon-o-calendar'),
                    ]),
                Section::make('Media')
                    ->description('Upload post images.')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->columns(2)
                    ->schema([
                        FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->image()
                            ->disk('public')
                            ->directory('blog')
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->helperText('Recommended: 1200x630px. Max 5MB.'),
                        FileUpload::make('og_image')
                            ->label('OG Image (Social Share)')
                            ->image()
                            ->disk('public')
                            ->directory('blog/og')
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->helperText('1200x630px recommended for social media sharing.'),
                    ]),
                Section::make('SEO')
                    ->description('Search engine optimization fields.')
                    ->icon(Heroicon::OutlinedMagnifyingGlass)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(255)
                            ->placeholder('Custom meta title (defaults to post title)')
                            ->prefixIcon('heroicon-o-tag'),
                        Textarea::make('meta_description')
                            ->maxLength(500)
                            ->rows(3)
                            ->placeholder('Custom meta description for search engines'),
                    ]),
            ]);
    }
}
