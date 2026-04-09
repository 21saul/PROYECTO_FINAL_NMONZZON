<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PRODUCTMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: PRODUCTS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_price',
        'sku',
        'stock',
        'stock_alert',
        'weight',
        'featured_image',
        'is_featured',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'        => 'required|max_length[200]',
        'slug'        => 'required|is_unique[products.slug,id,{id}]',
        'price'       => 'required|decimal',
        'category_id' => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateSlug'];
    protected $beforeUpdate   = ['generateSlug'];

    public function insert($row = null, bool $returnID = true)
    {
        if (isset($this->tempData['data'])) {
            if ($row === null) {
                $row = $this->tempData['data'];
            } else {
                $row = $this->transformDataToArray($row, 'insert');
                $row = array_merge($this->tempData['data'], $row);
            }
        } elseif ($row !== null) {
            $row = $this->transformDataToArray($row, 'insert');
        }

        $this->escape   = $this->tempData['escape'] ?? [];
        $this->tempData = [];

        if (is_array($row)) {
            $row = $this->applySlugFromName($row);
        }

        return parent::insert($row, $returnID);
    }

    public function update($id = null, $row = null): bool
    {
        if (isset($this->tempData['data'])) {
            if ($row === null) {
                $row = $this->tempData['data'];
            } else {
                $row = $this->transformDataToArray($row, 'update');
                $row = array_merge($this->tempData['data'], $row);
            }
        } elseif ($row !== null) {
            $row = $this->transformDataToArray($row, 'update');
        }

        $this->escape   = $this->tempData['escape'] ?? [];
        $this->tempData = [];

        if (is_array($row)) {
            $row = $this->applySlugFromName($row);
        }

        return parent::update($id, $row);
    }

    protected function applySlugFromName(array $row): array
    {
        $slug = trim((string) ($row['slug'] ?? ''));

        if ($slug === '' && ! empty($row['name'])) {
            helper('url');
            $row['slug'] = strtolower(url_title($row['name'], '-', true));
        }

        return $row;
    }

    protected function generateSlug(array $data): array
    {
        $data['data'] = $this->applySlugFromName($data['data'] ?? []);

        return $data;
    }

    public function getByCategory(int $categoryId): array
    {
        return $this->where('category_id', $categoryId)->findAll();
    }

    public function getFeatured(): array
    {
        return $this->where('is_featured', 1)->findAll();
    }

    public function getActive(): array
    {
        return $this->where('is_active', 1)->findAll();
    }

    public function getWithImages(int $id): ?array
    {
        $builder = $this->builder();

        if ($this->useSoftDeletes) {
            $builder->where($this->table . '.' . $this->deletedField, null);
        }

        $rows = $builder
            ->select(
                'products.*, product_images.id AS pi_id, product_images.image_url AS pi_image_url, '
                . 'product_images.alt_text AS pi_alt_text, product_images.sort_order AS pi_sort_order, '
                . 'product_images.is_primary AS pi_is_primary'
            )
            ->join('product_images', 'product_images.product_id = products.id', 'left')
            ->where('products.id', $id)
            ->orderBy('product_images.sort_order', 'ASC')
            ->get()
            ->getResultArray();

        if ($rows === []) {
            return null;
        }

        $product = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($rows[0] as $key => $value) {
            if (str_starts_with($key, 'pi_')) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $product[$key] = $value;
        }

        $images = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($rows as $row) {
            if ($row['pi_id'] === null) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $images[] = [
                'id'         => $row['pi_id'],
                'image_url'  => $row['pi_image_url'],
                'alt_text'   => $row['pi_alt_text'],
                'sort_order' => $row['pi_sort_order'],
                'is_primary' => $row['pi_is_primary'],
            ];
        }

        $product['images'] = $images;

        return $product;
    }
}