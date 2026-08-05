<?php

/**
 * Helper de Paginação
 * Gerencia paginação de resultados
 */

class Pagination {
    private int $total;
    private int $perPage;
    private int $currentPage;
    private string $url;
    private array $params;
    
    public function __construct(int $total, int $perPage = null, int $currentPage = 1, string $url = '', array $params = []) {
        $this->total = $total;
        $this->perPage = $perPage ?? Constants::PAGINATION_DEFAULT_LIMIT;
        $this->currentPage = max(1, $currentPage);
        $this->url = $url;
        $this->params = $params;
        
        // Limita o número por página
        $maxLimit = Constants::PAGINATION_MAX_LIMIT;
        if ($this->perPage > $maxLimit) {
            $this->perPage = $maxLimit;
        }
    }
    
    /**
     * Obtém o número total de páginas
     */
    public function getTotalPages(): int {
        return (int) ceil($this->total / $this->perPage);
    }
    
    /**
     * Obtém o offset para a query SQL
     */
    public function getOffset(): int {
        return ($this->currentPage - 1) * $this->perPage;
    }
    
    /**
     * Obtém o limite para a query SQL
     */
    public function getLimit(): int {
        return $this->perPage;
    }
    
    /**
     * Verifica se há uma página anterior
     */
    public function hasPreviousPage(): bool {
        return $this->currentPage > 1;
    }
    
    /**
     * Verifica se há uma próxima página
     */
    public function hasNextPage(): bool {
        return $this->currentPage < $this->getTotalPages();
    }
    
    /**
     * Obtém o número da página anterior
     */
    public function getPreviousPage(): int {
        return max(1, $this->currentPage - 1);
    }
    
    /**
     * Obtém o número da próxima página
     */
    public function getNextPage(): int {
        return min($this->getTotalPages(), $this->currentPage + 1);
    }
    
    /**
     * Obtém a URL para uma página específica
     */
    public function getUrl(int $page): string {
        $params = array_merge($this->params, ['page' => $page]);
        $queryString = http_build_query($params);
        
        return $this->url . ($queryString ? '?' . $queryString : '');
    }
    
    /**
     * Obtém a URL da página anterior
     */
    public function getPreviousUrl(): string {
        return $this->getUrl($this->getPreviousPage());
    }
    
    /**
     * Obtém a URL da próxima página
     */
    public function getNextUrl(): string {
        return $this->getUrl($this->getNextPage());
    }
    
    /**
     * Obtém a URL da primeira página
     */
    public function getFirstUrl(): string {
        return $this->getUrl(1);
    }
    
    /**
     * Obtém a URL da última página
     */
    public function getLastUrl(): string {
        return $this->getUrl($this->getTotalPages());
    }
    
    /**
     * Obtém o número de itens sendo exibidos
     */
    public function getFrom(): int {
        return min($this->total, $this->getOffset() + 1);
    }
    
    /**
     * Obtém o número do último item sendo exibido
     */
    public function getTo(): int {
        return min($this->total, $this->getOffset() + $this->perPage);
    }
    
    /**
     * Obtém um array com os números das páginas a serem exibidas
     */
    public function getPages(int $surrounding = 2): array {
        $totalPages = $this->getTotalPages();
        $currentPage = $this->currentPage;
        
        if ($totalPages <= 1) {
            return [1];
        }
        
        $pages = [];
        
        // Sempre mostra a primeira página
        $pages[] = 1;
        
        // Páginas ao redor da atual
        for ($i = max(2, $currentPage - $surrounding); $i <= min($totalPages - 1, $currentPage + $surrounding); $i++) {
            $pages[] = $i;
        }
        
        // Sempre mostra a última página
        if ($totalPages > 1) {
            $pages[] = $totalPages;
        }
        
        // Remove duplicatas e ordena
        $pages = array_unique($pages);
        sort($pages);
        
        return $pages;
    }
    
    /**
     * Obtém informações de paginação como array
     */
    public function toArray(): array {
        return [
            'total' => $this->total,
            'per_page' => $this->perPage,
            'current_page' => $this->currentPage,
            'total_pages' => $this->getTotalPages(),
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
            'has_previous' => $this->hasPreviousPage(),
            'has_next' => $this->hasNextPage(),
            'previous_page' => $this->getPreviousPage(),
            'next_page' => $this->getNextPage(),
            'previous_url' => $this->hasPreviousPage() ? $this->getPreviousUrl() : null,
            'next_url' => $this->hasNextPage() ? $this->getNextUrl() : null,
            'first_url' => $this->getFirstUrl(),
            'last_url' => $this->getLastUrl(),
            'pages' => $this->getPages()
        ];
    }
    
    /**
     * Renderiza a paginação HTML
     */
    public function render(string $view = 'pagination'): string {
        $data = $this->toArray();
        
        if ($data['total_pages'] <= 1) {
            return '';
        }
        
        ob_start();
        require __DIR__ . "/../views/components/{$view}.php";
        return ob_get_clean();
    }
}
