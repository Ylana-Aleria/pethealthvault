<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiagnosisResource\Pages;
use App\Models\Diagnosis;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class DiagnosisResource extends Resource
{
    protected static ?string $model = Diagnosis::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Medical Records';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('PetID')
                    ->relationship('pet', 'Name')
                    ->required()
                    ->searchable(),

                DatePicker::make('RecordDate')
                    ->required(),

                Select::make('Diagnosis')
                    ->options([
                        'Parvovirus' => 'Parvovirus',
                        'Distemper' => 'Distemper',
                        'Rabies' => 'Rabies',
                        'Fleas and Ticks' => 'Fleas and Ticks',
                        'Worms' => 'Worms',
                        'Arthritis' => 'Arthritis',
                        'Ear Infection' => 'Ear Infection',
                        'Skin Allergy' => 'Skin Allergy',
                    ])
                    ->required()
                    ->searchable(),

                Select::make('MedicationID')
                    ->relationship('medication', 'ProductName')
                    ->nullable()
                    ->searchable(),

                TextInput::make('Veterinarian')
                    ->maxLength(100)
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('DiagnosisID')->label('ID')->sortable(),
                TextColumn::make('pet.Name')->label('Pet Name')->sortable()->searchable(),
                TextColumn::make('RecordDate')->sortable(),
                TextColumn::make('Diagnosis')->sortable()->searchable(),
                TextColumn::make('medications.ProductName')->label('Medication')->sortable()->searchable(),
                TextColumn::make('Veterinarian')->sortable()->searchable(),
                TextColumn::make('created_at')->dateTime('M d, Y')->label('Created At'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiagnoses::route('/'),
            'create' => Pages\CreateDiagnosis::route('/create'),
            'edit' => Pages\EditDiagnosis::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->select(['DiagnosisID', 'PetID', 'RecordDate', 'Diagnosis', 'MedicationID', 'Veterinarian', 'created_at'])
            ->with(['pet:PetID,Name', 'medication:MedicationID,ProductName']);
    }
}
