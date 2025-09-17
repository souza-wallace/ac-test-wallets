import { useState } from "react";
import { Link } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useToast } from "@/hooks/use-toast";
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

interface Transaction {
  id: string;
  type: "deposit" | "transfer_in" | "transfer_out";
  amount: number;
  description: string;
  recipient?: string;
  sender?: string;
  date: string;
  status: "completed" | "pending" | "failed" | "reversed";
  canReverse: boolean;
}

const Historico = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [statusFilter, setStatusFilter] = useState("all");
  const [typeFilter, setTypeFilter] = useState("all");
  const { toast } = useToast();

  const transactions: Transaction[] = [
    {
      id: "1",
      type: "deposit",
      amount: 500.00,
      description: "Depósito via PIX",
      date: "2024-01-15T10:30:00Z",
      status: "completed",
      canReverse: true
    },
    {
      id: "2",
      type: "transfer_out",
      amount: -150.25,
      description: "Transferência para Maria Santos",
      recipient: "maria.santos@email.com",
      date: "2024-01-14T15:45:00Z",
      status: "completed",
      canReverse: true
    },
    {
      id: "3",
      type: "transfer_in",
      amount: 75.50,
      description: "Recebido de Pedro Costa",
      sender: "pedro.costa@email.com",
      date: "2024-01-13T09:20:00Z",
      status: "completed",
      canReverse: false
    },
    {
      id: "4",
      type: "transfer_out",
      amount: -200.00,
      description: "Pagamento da conta de luz",
      recipient: "contaspagar@energia.com",
      date: "2024-01-12T14:30:00Z",
      status: "pending",
      canReverse: true
    },
    {
      id: "5",
      type: "deposit",
      amount: 1000.00,
      description: "Depósito via Transferência Bancária",
      date: "2024-01-11T08:15:00Z",
      status: "failed",
      canReverse: false
    },
    {
      id: "6",
      type: "transfer_out",
      amount: -300.00,
      description: "Transferência cancelada",
      recipient: "joao.silva@email.com",
      date: "2024-01-10T16:20:00Z",
      status: "reversed",
      canReverse: false
    }
  ];

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
      failed: { label: "Falhou", variant: "destructive" as const },
      reversed: { label: "Estornada", variant: "outline" as const }
    };
    
    const config = statusMap[status as keyof typeof statusMap];
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
    const matchesSearch = transaction.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         transaction.recipient?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         transaction.sender?.toLowerCase().includes(searchTerm.toLowerCase());
    
    const matchesStatus = statusFilter === "all" || transaction.status === statusFilter;
    const matchesType = typeFilter === "all" || transaction.type === typeFilter;
    
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
                  filteredTransactions.map((transaction) => (
                    <div
                      key={transaction.id}
                      className="flex items-center justify-between p-4 rounded-lg border border-border hover:bg-card-hover transition-colors"
                    >
                      <div className="flex items-center gap-4">
                        <div className="w-10 h-10 bg-muted/50 rounded-full flex items-center justify-center">
                          {getTransactionIcon(transaction.type)}
                        </div>
                        <div className="space-y-1">
                          <div className="flex items-center gap-2">
                            <p className="font-medium">{transaction.description}</p>
                            <Badge variant="outline" className="text-xs">
                              {getTypeLabel(transaction.type)}
                            </Badge>
                          </div>
                          <div className="text-sm text-muted-foreground space-y-1">
                            <p>{formatDate(transaction.date)}</p>
                            {transaction.recipient && (
                              <p>Para: {transaction.recipient}</p>
                            )}
                            {transaction.sender && (
                              <p>De: {transaction.sender}</p>
                            )}
                          </div>
                        </div>
                      </div>
                      
                      <div className="text-right space-y-2">
                        <div className="space-y-1">
                          <p className={`font-semibold ${
                            transaction.amount > 0 ? 'text-success' : 'text-destructive'
                          }`}>
                            {transaction.amount > 0 ? '+' : ''}{formatCurrency(transaction.amount)}
                          </p>
                          {getStatusBadge(transaction.status)}
                        </div>
                        
                        {transaction.canReverse && transaction.status === "completed" && (
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => handleReverseTransaction(transaction.id, transaction.description)}
                            className="text-xs"
                          >
                            <RotateCcw className="w-3 h-3 mr-1" />
                            Estornar
                          </Button>
                        )}
                      </div>
                    </div>
                  ))
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