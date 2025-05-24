<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PetResource\Pages;
use App\Models\Pet;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Tables\Columns\ImageColumn;

class PetResource extends Resource
{
    protected static ?string $model = Pet::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Medical Records';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('Name')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('Image')
                            ->disk('public') 
                            ->directory('') 
                            ->nullable(), 
                        Select::make('Species')
                            ->options([
                                'Canine' => 'Canine',
                                'Feline' => 'Feline',
                            ])
                            ->required()
                            ->reactive(),

                        Select::make('Breed')
                            ->required()
                            ->options(fn(Get $get) => match ($get('Species')) {
                                'Canine' => [
                                    'Labrador Retriever' => 'Labrador Retriever',
                                    'German Shepherd' => 'German Shepherd',
                                    'Golden Retriever' => 'Golden Retriever',
                                    'Bulldog' => 'Bulldog',
                                    'Beagle' => 'Beagle',
                                    'Poodle' => 'Poodle',
                                    'Shih Tzu' => 'Shih Tzu',
                                    'Siberian Husky' => 'Siberian Husky',
                                    'Chihuahua' => 'Chihuahua',
                                ],
                                'Feline' => [
                                    'Persian' => 'Persian',
                                    'Siamese' => 'Siamese',
                                    'Maine Coon' => 'Maine Coon',
                                    'Ragdoll' => 'Ragdoll',
                                    'Bengal' => 'Bengal',
                                    'Sphynx' => 'Sphynx',
                                    'British Shorthair' => 'British Shorthair',
                                    'Abyssinian' => 'Abyssinian',
                                    'Scottish Fold' => 'Scottish Fold',
                                ],
                                default => [],
                            })
                            ->disabled(fn(Get $get) => $get('Species') === null)
                            ->searchable(),
                        Forms\Components\TextInput::make('AgeYears')
                            ->nullable()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20),

                        Forms\Components\TextInput::make('AgeMonths')
                            ->nullable()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(11),

                        Forms\Components\Select::make('Gender')
                            ->options([
                                'Male' => 'Male',
                                'Female' => 'Female',
                            ])
                            ->required(),
                        Forms\Components\Select::make('UserID')
                            ->label('Owner')
                            ->relationship('user', 'firstname')
                            ->required(),
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('PetID')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('Image')
                    ->getStateUsing(fn($record) => asset('storage/' . $record->Image))
                    ->circular()
                    ->size(70),
                Tables\Columns\TextColumn::make('UserID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Species')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Breed')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Age')
                    ->label('Age')
                    ->getStateUsing(function ($record) {
                        $ageYears = $record->AgeYears ?? 0;
                        $ageMonths = $record->AgeMonths ?? 0;

                        return $ageYears . ' years ' . $ageMonths . ' months';
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('Gender')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.firstname')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPets::route('/'),
            'create' => Pages\CreatePet::route('/create'),
            'edit' => Pages\EditPet::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->select(['PetID', 'UserID', 'Name', 'Species', 'Breed', 'AgeYears', 'AgeMonths', 'Gender', 'Image', 'created_at'])
            ->with(['user:id,firstname,lastname']);
    }
}
