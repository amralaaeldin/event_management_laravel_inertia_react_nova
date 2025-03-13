<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class Event extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Event>
     */
    public static $model = \App\Models\Event::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = ['id', 'name', 'location', 'status'];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()
                ->textAlign('left')
                ->sortable(),

            Text::make('Name')
                ->textAlign('left')
                ->rules('required',  'max:255', 'unique:events,name,{{resourceId}}'),

            DateTime::make('Start Date & Time', 'start_date_time')
                ->textAlign('left')
                ->sortable()
                ->rules('required', 'date', 'after_or_equal:today'),

            Number::make('Duration (Minutes)', 'duration')
                ->textAlign('left')
                ->rules('required', 'integer', 'min:1'),

            Markdown::make('Description')
                ->textAlign('left')
                ->rules('required', 'max:1000'),

            Text::make('Location')
                ->textAlign('left')
                ->rules('required', 'max:255'),

            Select::make('Status')
                ->textAlign('left')
                ->options(\App\Models\Event::STATUSES)
                ->displayUsingLabels()
                ->rules('required'),

            Number::make('Capacity')
                ->textAlign('left')
                ->rules('required', 'integer', 'min:1'),

            Number::make('Waitlist Capacity', 'waitlist_capacity')
                ->textAlign('left')
                ->rules('required', 'integer', 'min:0'),

            BelongsToMany::make('Attendees', 'attendees', User::class)
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            BelongsToMany::make('Wishlist Users', 'wishlistUsers', User::class)
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating(),
        ];
    }

    /**
     * Get the cards available for the resource.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array<int, \Laravel\Nova\Lenses\Lens>
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Laravel\Nova\Actions\Action>
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
