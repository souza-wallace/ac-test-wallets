import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useToast } from "@/hooks/use-toast";
import { api, Transaction } from "@/services/api";
import { 
  ArrowLeft, 
  History, 
  Search, 
  Filter,
  Plus, 
  ArrowUpRight, 
  ArrowDownLeft,
  RotateCcw,
  Download
} from "lucide-react";

const Historico = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [statusFilter, setStatusFilter] = useState("all");
  const [typeFilter, setTypeFilter] = useState("all");
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const { toast } = useToast();

  useEffect(() => {
    loadTransactions();
  }, [currentPage]);

  const loadTransactions = async () => {
    try {
      console.log('lskjhkjh')
      setLoading(true);
      const response = await api.getTransactions(currentPage, 15);
      
      if (response.error) {
        toast({
          title: "Erro ao carregar transações",
          description: response.error,
          variant: "destructive",
        });
      } else {
        setTransactions(response.data || []);
        setTotalPages(response.last_page || 1);
      }
    } catch (error) {
      toast({
        title: "Erro de conexão",
        description: "Não foi possível carregar as transações",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(value);
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const getTransactionType = (type: string) => {
    switch (type.toLowerCase()) {
      case 'deposit': return 'deposit';
      case 'transfer': return 'transfer_out';
      case 'reversal': return 'transfer_in';
      default: return 'deposit';
    }
  };

  const getTransactionIcon = (type: string) => {
    switch (type) {
      case "deposit":
        return <Plus className="w-4 h-4 text-success" />;
      case "transfer_in":
        return <ArrowDownLeft className="w-4 h-4 text-success" />;
      case "transfer_out":
        return <ArrowUpRight className="w-4 h-4 text-destructive" />;
      default:
        return <History className="w-4 h-4" />;
    }
  };

  const getStatusBadge = (status: string) => {
    const statusMap = {
      completed: { label: "Concluída", variant: "default" as const },
      pending: { label: "Pendente", variant: "secondary" as const },
      reversed: { label: "Revertida", variant: "destructive" as const },
    };
  
    const key = status.toLowerCase() as keyof typeof statusMap;
    const config = statusMap[key];
    if (!config) return <Badge variant="outline">Desconhecido</Badge>;
  
    return <Badge variant={config.variant}>{config.label}</Badge>;
  };  

  const getTypeLabel = (type: string) => {
    const typeMap = {
      deposit: "Depósito",
      transfer_in: "Recebimento",
      transfer_out: "Transferência"
    };
    return typeMap[type as keyof typeof typeMap] || type;
  };

  const handleReverseTransaction = (transactionId: string, description: string) => {
    toast({
      title: "TRANSAÇÃO ESTORNADA",
      description: `"${description}" foi estornada com sucesso.`,
    });
  };

  const filteredTransactions = transactions.filter(transaction => {
    const matchesSearch = transaction.description?.toLowerCase().includes(searchTerm.toLowerCase()) || false;
    const matchesStatus = statusFilter === "all" || transaction.status === statusFilter;
    const transactionType = getTransactionType(transaction.type);
    const matchesType = typeFilter === "all" || transactionType === typeFilter;
    
    return matchesSearch && matchesStatus && matchesType;
  });

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <header className="border-b border-border bg-card">
        <div className="max-w-6xl mx-auto px-4 py-4 flex items-center gap-4">
          <Link to="/dashboard">
            <Button variant="outline" size="sm">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Voltar
            </Button>
          </Link>
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-muted rounded-full flex items-center justify-center">
              <History className="w-5 h-5 text-muted-foreground" />
            </div>
            <div>
              <h1 className="font-semibold text-lg">Histórico de transações</h1>
              <p className="text-sm text-muted-foreground">Todas as suas movimentações</p>
            </div>
          </div>
        </div>
      </header>

      <div className="max-w-6xl mx-auto px-4 py-8">
        <div className="grid gap-6">
          {/* Filters */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Filter className="w-5 h-5" />
                Filtros
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div className="space-y-2">
                  <label className="text-sm font-medium">Buscar</label>
                  <div className="relative">
                    <Search className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <Input
                      placeholder="Descrição, destinatário..."
                      value={searchTerm}
                      onChange={(e) => setSearchTerm(e.target.value)}
                      className="pl-10"
                    />
                  </div>
                </div>

                <div className="space-y-2">
                  <label className="text-sm font-medium">Status</label>
                  <Select value={statusFilter} onValueChange={setStatusFilter}>
                    <SelectTrigger>
                      <SelectValue placeholder="Todos os status" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">Todos os status</SelectItem>
                      <SelectItem value="completed">Concluída</SelectItem>
                      <SelectItem value="pending">Pendente</SelectItem>
                      <SelectItem value="failed">Falhou</SelectItem>
                      <SelectItem value="reversed">Estornada</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2">
                  <label className="text-sm font-medium">Tipo</label>
                  <Select value={typeFilter} onValueChange={setTypeFilter}>
                    <SelectTrigger>
                      <SelectValue placeholder="Todos os tipos" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">Todos os tipos</SelectItem>
                      <SelectItem value="deposit">Depósito</SelectItem>
                      <SelectItem value="transfer_in">Recebimento</SelectItem>
                      <SelectItem value="transfer_out">Transferência</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2">
                  <label className="text-sm font-medium">Ações</label>
                  <Button variant="outline" className="w-full">
                    <Download className="w-4 h-4 mr-2" />
                    Exportar
                  </Button>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Transactions List */}
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <div>
                  <CardTitle>Transações</CardTitle>
                  <CardDescription>
                    {filteredTransactions.length} transações encontradas
                  </CardDescription>
                </div>
              </div>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {filteredTransactions.length === 0 ? (
                  <div className="text-center py-8">
                    <History className="w-12 h-12 mx-auto mb-4 text-muted-foreground" />
                    <p className="text-muted-foreground">Nenhuma transação encontrada</p>
                  </div>
                ) : (
                  loading ? (
                    <div className="text-center py-8">
                      <p className="text-muted-foreground">Carregando transações...</p>
                    </div>
                  ) : (
                    filteredTransactions.map((transaction) => {
                      const transactionType = getTransactionType(transaction.type);
                      return (
                        <div
                          key={transaction.id}
                          className="flex items-center justify-between p-4 rounded-lg border border-border hover:bg-card-hover transition-colors"
                        >
                          <div className="flex items-center gap-4">
                            <div className="w-10 h-10 bg-muted/50 rounded-full flex items-center justify-center">
                              {getTransactionIcon(transactionType)}
                            </div>
                            <div className="space-y-1">
                              <div className="flex items-center gap-2">
                                <p className="font-medium">{transaction.description || 'Transação'}</p>
                                <Badge variant="outline" className="text-xs">
                                  {getTypeLabel(transactionType)}
                                </Badge>
                              </div>
                              <div className="text-sm text-muted-foreground space-y-1">
                                <p>{formatDate(transaction.created_at)}</p>
                                {transaction.related_wallet && (
                                  <p>Carteira relacionada: {transaction.related_wallet}</p>
                                )}
                              </div>
                            </div>
                          </div>
                          
                          <div className="text-right space-y-2">
                            <div className="space-y-1">
                              <p className={`font-semibold ${
                                transactionType === 'deposit' || transactionType === 'transfer_in' ? 'text-success' : 'text-destructive'
                              }`}>
                                {(transactionType === 'deposit' || transactionType === 'transfer_in') ? '+' : '-'}{formatCurrency(Math.abs(transaction.amount))}
                              </p>
                              {getStatusBadge(transaction.status)}
                            </div>
                            
                            {transaction.status === "completed" && (
                              <Button
                                variant="outline"
                                size="sm"
                                onClick={() => handleReverseTransaction(transaction.id.toString(), transaction.description || 'Transação')}
                                className="text-xs"
                              >
                                <RotateCcw className="w-3 h-3 mr-1" />
                                Estornar
                              </Button>
                            )}
                          </div>
                        </div>
                      );
                    })
                  )
                )}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default Historico;