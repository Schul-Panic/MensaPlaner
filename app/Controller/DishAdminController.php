<?php

namespace App\Controller;

use App\Model\Dish;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DishAdminController
{
    public function showDishManagement(Request $request, Response $response): Response
    {
        $dishesByCategory = array_fill_keys(array_keys(Dish::ROW_LABELS), []);

        foreach ((new Dish())->all() as $dish) {
            $dishesByCategory[$dish['category']][] = $dish;
        }

        ob_start();
        require __DIR__ . '/../View/dish-management.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function newDish(Request $request, Response $response): Response
    {
        $dish = null;
        $prefillCategory = $request->getQueryParams()['category'] ?? null;
        $allAllergens = (new Dish())->allergens();
        $selectedAllergens = [];

        ob_start();
        require __DIR__ . '/../View/dish-form.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function createDish(Request $request, Response $response): Response
    {
        $parsed = $this->parseDishInput($request);

        if (!$parsed) {
            $_SESSION['flash_error'] = 'Bitte alle Felder gültig ausfüllen.';

            return $response->withHeader('Location', '/speiseplan/verwaltung/neu')->withStatus(302);
        }

        [$data, $allergenNames] = $parsed;
        $dishModel = new Dish();
        $dishId = $dishModel->create($data);
        $dishModel->syncAllergens($dishId, $dishModel->resolveOrCreateAllergenIds($allergenNames));

        return $response->withHeader('Location', '/speiseplan/verwaltung')->withStatus(302);
    }

    public function editDish(Request $request, Response $response, array $args): Response
    {
        $dish = (new Dish())->find((int) $args['id']);

        if (!$dish) {
            return $response->withHeader('Location', '/speiseplan/verwaltung')->withStatus(302);
        }

        $allAllergens = (new Dish())->allergens();
        $selectedAllergens = $dish['allergens'];

        ob_start();
        require __DIR__ . '/../View/dish-form.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);

        return $response;
    }

    public function updateDish(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $parsed = $this->parseDishInput($request);

        if (!$parsed) {
            $_SESSION['flash_error'] = 'Bitte alle Felder gültig ausfüllen.';

            return $response->withHeader('Location', "/speiseplan/verwaltung/{$id}/edit")->withStatus(302);
        }

        [$data, $allergenNames] = $parsed;
        $dishModel = new Dish();
        $dishModel->update($id, $data);
        $dishModel->syncAllergens($id, $dishModel->resolveOrCreateAllergenIds($allergenNames));

        return $response->withHeader('Location', '/speiseplan/verwaltung')->withStatus(302);
    }

    public function deleteDish(Request $request, Response $response, array $args): Response
    {
        (new Dish())->delete((int) $args['id']);

        return $response->withHeader('Location', '/speiseplan/verwaltung')->withStatus(302);
    }

    private function parseDishInput(Request $request): ?array
    {
        $data = $request->getParsedBody();
        $category = $data['category'] ?? '';
        $variant = $data['variant'] ?? '';
        $name = trim($data['name'] ?? '');
        $price = $this->normalizePrice($data['price'] ?? '');
        $labels = trim($data['labels'] ?? '');

        if (
            !array_key_exists($category, Dish::ROW_LABELS)
            || !array_key_exists($variant, Dish::COLUMN_LABELS)
            || $name === ''
            || $price === null
        ) {
            return null;
        }

        $allergenNames = array_filter(array_map('trim', explode(',', $labels)));

        return [
            [
                'category' => $category,
                'variant' => $variant,
                'name' => $name,
                'price' => $price,
            ],
            $allergenNames,
        ];
    }

    private function normalizePrice(string $price): ?string
    {
        $price = trim(str_replace('€', '', $price));
        $price = str_replace(',', '.', $price);

        if (!is_numeric($price)) {
            return null;
        }

        return number_format((float) $price, 2, ',', '.') . ' €';
    }
}
