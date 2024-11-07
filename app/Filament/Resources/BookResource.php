<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Book Information')
                    ->columns(2)
                    ->schema([
                        //add title field
                        TextInput::make('title')
                            ->label('Title')
                            ->required(),
                        //add image field
                        FileUpload::make('image')
                            ->image()
                            ->required(),
                        TextInput::make('author')
                            ->label('Author')
                            ->required(),
                        RichEditor::make('description')
                            ->label('Description')
                            ->required()
                            ->columnSpanFull(),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //add title column
                TextColumn::make('title')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                ImageColumn::make('image')
                    ->width(200)
                    ->height(200)
                    ->circular(),
                TextColumn::make('author')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(isIndividual: true)
                    ->sortable()
                    ->wrap(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->action(function (Model $record) {
                        if ($record->author === 'Prof. Orville Stamm') {
                            //show failure notification
                            Notification::make()
                                ->title('Operation failed')
                                ->body('Prof. Orville Stamm cannot be deleted.')
                                ->danger()
                                ->send();
                            //cancel the action
                            return;
                        }
                        try {
                            //check if the image exists in storage
                            if (Storage::disk('public')->exists($record->image)) {
                                //delete the image from storage
                                Storage::disk('public')->delete($record->image);
                            }
                            $record->delete();
                            //show success notification
                            Notification::make()
                                ->title('Deleted successfully')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            //show error notification
                            Notification::make()
                                ->title('Error')
                                ->body('An error occurred while deleting the book.')
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
