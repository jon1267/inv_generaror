<?php

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use App\Models\Template;
use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.public')] class extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public ?int $selectedTemplateId = 1;

    public function mount(): void
    {
        $this->form->fill([
            'template_id' => 1,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'tax_rate' => 10,
            'items' => [
                [
                    'description' => '',
                    'quantity' => 1,
                    'unit_price' => 0,
                ]
            ],
        ]);
    }

    // the form
    public function form(Schema $form): Schema
    {
        return $form->schema([
            Grid::make(2)
                ->schema([
                    Section::make('Company Info')
                        ->description('Your business details')
                        ->schema([
                            TextInput::make('company_name')
                                ->label('Company Name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Your Company Name'),
                            Textarea::make('company_address')
                                ->label('Company Address')
                                ->rows(3)
                                ->placeholder('123 Main St, City, Country'),
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('company_email'
                                        ->label('Email')
                                        ->email()
                                        ->placeholder('hello@company.com'),

                                    TextInput::make('company_phone'
                                        ->label('Phone')
                                        ->tel()
                                        ->placeholder('+1 (555) 123-4567'),
                            ]),
                    ])
                ])->columnSpan(1),

                // Client Information
                Section::make('Client Information')
                    ->description('Bill to')
                    ->schema([
                        TextInput::make('client_name')
                            ->label('Client Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Client Company Name')
                            ->live(debounce: 500),

                        Textarea::make('client_address')
                            ->label('Address')
                            ->rows(3)
                            ->placeholder('456 Client Avenue, City, Country')
                            ->live(debounce: 500),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('client_email')
                                    ->label('Email')
                                    ->email()
                                    ->placeholder('contact@client.com')
                                    ->live(debounce: 500),

                                TextInput::make('client_phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->placeholder('+1 (555) 987-6543')
                                    ->live(debounce: 500),
                            ]),
                    ])
                    ->columnSpan(1),

                    Grid::make(3)
                        ->schema([
                            DatePicker::make('invoice_date')
                                ->label('Invoice Date')
                                ->required()
                                ->default(now())
                                ->native(false),

                            DatePicker::make('due_date')
                                ->label('Due Date')
                                ->required()
                                ->default(now()->addDays(30))
                                ->native(false),

                            TextInput::make('tax_rate')
                                ->label('Tax Rate (%)')
                                ->numeric()
                                ->default(18)
                                ->suffix('%')
                                ->live(onBlur: true),
                        ]),

            Section::make('Line Items')
                ->schema([
                    Repeater::make('items')
                        ->schema([
                            Grid::make(4)
                                ->schema([
                                    TextInput::make('description')
                                        ->label('Description')
                                        ->required()
                                        ->placeholder('Service or product description')
                                        ->columnSpan(2)
                                        ->live(debounce: 500),

                                    TextInput::make('quantity')
                                        ->label('Quantity')
                                        ->numeric()
                                        ->default(1)
                                        ->required()
                                        ->minValue(1)
                                        ->live(onBlur: true)
                                        ->columnSpan(1),

                                    TextInput::make('unit_price')
                                        ->label('Unit Price')
                                        ->numeric()
                                        ->prefix('$')
                                        ->required()
                                        ->default(0)
                                        ->live(onBlur: true)
                                        ->columnSpan(1),
                                ]),
                        ])
                        // ->defaultItems(1)
                        ->addActionLabel('Add Line Item')
                        ->reorderable()
                        ->cloneable()
                        ->deleteAction(
                            fn($action) => $action->requiresConfirmation()
                        ),
                ]),

            Grid::make(2)
                ->schema([
                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3)
                        ->placeholder('Additional notes or special instructions')
                        ->columnSpan(1)
                        ->live(debounce: 500),

                    Textarea::make('terms')
                        ->label('Payment Terms')
                        ->rows(3)
                        ->placeholder('Payment is due within 30 days')
                        ->columnSpan(1)
                        ->live(debounce: 500),
                ]),

             Select::make('template_id')
                 ->label('Invoice Template')
                 ->options(Template::active()->pluck('name', 'id'))
                 ->default(1)
                 ->required()
                 ->live()
                 ->afterStateUpdated(function ($state) {
                     $this->selectedTemplateId = false;
                 })

        ])->statePath('data');
    }

    public function getSubtotal()
    {
        $items = [];
    }

    public function with(): array
    {
        return [
            'title' => 'Create Invoice',
        ];
    }
};
?>

<div>
    <h1 class="text-2xl font-bold">Create Invoice</h1>
    <p class="text-gray-600 mt-2">Fill in your invoice details below</p>
</div>