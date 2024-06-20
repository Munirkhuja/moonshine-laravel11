<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\StatusClientUserEnum;
use App\Models\ClientUser;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Components\TableBuilder;
use MoonShine\Decorations\Block;
use MoonShine\Exceptions\FieldException;
use MoonShine\Fields\Enum;
use MoonShine\Fields\Field;
use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;

/**
 * @extends ModelResource<ClientUser>
 */
class ClientUserResource extends ModelResource
{
    protected string $model = ClientUser::class;

    protected string $title = 'ClientUsers';

    /**
     * @return list<MoonShineComponent|Field>
     * @throws FieldException
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable()->readonly(),
                Text::make(trans('moonshine::ui.resource.phone'), 'phone')
                    ->readonly(),
                Enum::make(trans('moonshine::ui.resource.status'), 'status')
                    ->attach(StatusClientUserEnum::class),
                Text::make(trans('moonshine::ui.resource.created_at'), 'created_at')
                    ->sortable()->readonly(),

                HasMany::make(
                    trans('moonshine::ui.resource.telegram_bot_user_send_message'),
                    'client_user_messages',
                    resource: new ClientUserMessageResource()
                )->hideOnIndex()->modifyTable(
                    fn(TableBuilder $table, bool $preview) => $table
                        ->buttons((new ClientUserMessageResource())->getIndexItemButtons())
                )
            ]),
        ];
    }

    protected function afterUpdated(Model $item): Model
    {
        $chat = TelegraphChat::query()->where('chat_id', $item->t_chat_id)->firstOrFail();
        $chat->message('You account with phone:' . $item->phone . ', has been ' . $item->status)->send();
        return $item;
    }

    /**
     * @param ClientUser $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
