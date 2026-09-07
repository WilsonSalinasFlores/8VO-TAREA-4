<?php

namespace App\Models;

use ArrayAccess;

class Contacto implements ArrayAccess
{
    public ?int $id;
    public string $nombres;
    public string $apellidos;
    public string $tipo;
    public ?string $direccion;
    public ?string $telefono;
    public array $telefonos;
    public ?string $sitio_web;
    public ?string $empresa;
    public ?string $cargo;

    public function __construct(array $attributes = [])
    {
        $this->id = $attributes['id'] ?? null;
        $this->nombres = $attributes['nombres'] ?? '';
        $this->apellidos = $attributes['apellidos'] ?? '';
        $this->tipo = $attributes['tipo'] ?? 'Personal';
        $this->direccion = $attributes['direccion'] ?? null;
        $this->telefono = $attributes['telefono'] ?? null;
        $this->telefonos = $attributes['telefonos'] ?? (
            !empty($this->telefono) ? [['numero' => $this->telefono, 'tipo' => 'Principal']] : []
        );
        $this->sitio_web = $attributes['sitio_web'] ?? null;
        $this->empresa = $attributes['empresa'] ?? null;
        $this->cargo = $attributes['cargo'] ?? null;
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public static function collection(array $items): array
    {
        return array_map(fn($item) => self::fromArray($item), $items);
    }

    public function nombreCompleto(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }

    // ArrayAccess implementation for seamless compatibility with $contacto['prop'] in Blade
    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (property_exists($this, $offset)) {
            $this->$offset = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        if (property_exists($this, $offset)) {
            $this->$offset = null;
        }
    }
}
