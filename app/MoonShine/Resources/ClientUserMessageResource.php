<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\ClientUserMessage;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Decorations\Block;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Field;
use MoonShine\Fields\ID;
use MoonShine\Fields\Markdown;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;

/**
 * @extends ModelResource<ClientUserMessage>
 */
class ClientUserMessageResource extends ModelResource
{
    protected string $model = ClientUserMessage::class;

    protected string $title = 'ClientUserMessages';
    protected bool $detailInModal = true;


    /**
     * @return list<MoonShineComponent|Field>
     * @throws FieldException
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable()
                    ->readonly(),
                Markdown::make(trans('moonshine::ui.resource.message'), 'message')
                    ->readonly(),
                Text::make(trans('moonshine::ui.resource.created_at'), 'created_at')
                    ->sortable()
                    ->readonly(),
            ]),
        ];
    }

    public function getIndexItemButtons(): array
    {
        return [
            $this->getDetailButton(
                isAsync: $this->isAsync()
            ),
        ];
    }

    /**
     * @param ClientUserMessage $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
