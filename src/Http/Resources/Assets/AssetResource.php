<?php

namespace GetCandy\Api\Http\Resources\Assets;

use GetCandy\Api\Http\Resources\AbstractResource;
use GetCandy\Api\Http\Resources\Tags\TagCollection;

class AssetResource extends AbstractResource
{
    public function payload()
    {
        $data = [
            'id' => $this->encodedId(),
            'title' => $this->title,
            'caption' => $this->caption,
            'kind' => $this->kind,
            'external' => (bool) $this->external,
            'thumbnail' => $this->getThumbnail($this),
            'position' => (int) $this->position,
            'primary' => (bool) $this->primary,
            'url' => $this->getUrl($this),
        ];

        if (! $this->external) {
            $data = array_merge($data, [
                'sub_kind' => $this->sub_kind,
                'extension' => $this->extension,
                'original_filename' => $this->original_filename,
                'size' => $this->size,
                'width' => $this->width,
                'height' => $this->height,
                'url' => $this->getUrl($this),
            ]);
        } else {
            $data['url'] = $this->location;
        }

        return $data;
    }

    protected function getThumbnail($asset)
    {
        $transform = $asset->transforms->filter(function ($transform) {
            return $transform->transform->handle == 'thumbnail';
        })->first();

        if (! $transform) {
            return;
        }

        $path = $transform->location.'/'.$transform->filename;

        return Storage::disk($asset->source->disk)->url($path);
    }

    protected function getUrl($asset)
    {
        $path = $asset->location.'/'.$asset->filename;

        return Storage::disk($asset->source->disk)->url($path);
    }

    public function includes()
    {
        return [
            'transforms' => new AssetTransformCollection($this->whenLoaded('transforms')),
            'tags' => new TagCollection($this->whenLoaded('tags')),
        ];
    }
}
