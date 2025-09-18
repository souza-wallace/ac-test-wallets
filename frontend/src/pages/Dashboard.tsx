import { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { useToast } from "@/hooks/use-toast";
import { api, Transaction } from "@/services/api";
import { 
  Wallet, 
  ArrowUpRight, 
  ArrowDownLeft, 
  Plus, 
  History, 
  Eye, 
  EyeOff,
  LogOut 
} from "lucide-react";

const Dashboard = () => {
  const [showBalance, setShowBalance] = useState(true);
  const [balance, setBalance] = useState(0);
  const [userName, setUserName] = useState("");
  const [userEmail, setUserEmail] = useState("");
  const [recentTransactions, setRecentTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();
  const navigate = useNavigate();

  useEffect(() => {
    loadUserData();
    loadRecentTransactions();
  }, []);

  const loadUserData = async () => {
    try {
      const response = await api.getUserProfile();
      if (response.error) {
        toast({
          title: "Erro ao carregar dados",
          description: response.error,
          variant: "destructive",
        });
      } else if (response.data) {
        setUserName(response.data.name);
        setUserEmail(response.data.email);
        setBalance(response.data.wallet?.balance || 0);
      }
    } catch (error) {
      toast({
        title: "Erro de conexão",
        description: "Não foi possível carregar os dados do usuário",
        variant: "destructive",
      });
    }
  };

  const loadRecentTransactions = async () => {
    try {
      const response = await api.getTransactions(1, 3);
      if (response.error) {
        toast({
          title: "Erro ao carregar transações",
          description: response.error,
          variant: "destructive",
        });
      } else {
        setRecentTransactions(response.data?.slice(0, 3) || []);
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

  const handleLogout = () => {
    localStorage.removeItem('token');
    navigate('/login');
  };

  const getTransactionType = (type: string) => {
    switch (type.toLowerCase()) {
      case 'deposit': return 'deposit';
      case 'transfer': return 'transfer_out';
      case 'reversal': return 'transfer_in';
      default: return 'deposit';
    }
  };

  const mockTransactions: any[] = [
    {
      id: "1",
      type: "deposit",
      amount: 500.00,
      description: "Depósito via PIX",
      date: "2024-01-15T10:30:00Z",
      status: "completed"
    },
    {
      id: "2",
      type: "transfer_out",
      amount: -150.25,
      description: "Transferência para Maria Santos",
      date: "2024-01-14T15:45:00Z",
      status: "completed"
    },
    {
      id: "3",
      type: "transfer_in",
      amount: 75.50,
      description: "Recebido de Pedro Costa",
      date: "2024-01-13T09:20:00Z",
      status: "completed"
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
        return <Wallet className="w-4 h-4" />;
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
  
  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <header className="border-b border-border bg-card">
        <div className="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
              <Wallet className="w-5 h-5 text-primary-foreground" />
            </div>
            <div>
              <h1 className="font-semibold text-lg">Minha Carteira</h1>
              <p className="text-sm text-muted-foreground">Bem-vindo, {userName}</p>
            </div>
          </div>
          
          <div className="flex items-center gap-4">
            <div className="flex items-center gap-2">
              <Avatar>
                <AvatarFallback>JS</AvatarFallback>
              </Avatar>
              <div className="hidden md:block">
                <p className="text-sm font-medium">{userName}</p>
                <p className="text-xs text-muted-foreground">{userEmail}</p>
              </div>
            </div>
            <Button variant="outline" size="sm" onClick={handleLogout}>
              <LogOut className="w-4 h-4 mr-2" />
              Sair
            </Button>
          </div>
        </div>
      </header>

      <div className="max-w-6xl mx-auto px-4 py-8">
        <div className="grid gap-6">
          {/* Balance Card */}
          <Card className="bg-gradient-to-r from-primary to-primary-hover text-primary-foreground">
            <CardHeader>
              <div className="flex items-center justify-between">
                <div>
                  <CardDescription className="text-primary-foreground/70">
                    Saldo disponível
                  </CardDescription>
                  <CardTitle className="text-3xl font-bold flex items-center gap-2">
                    {showBalance ? formatCurrency(balance) : "••••••"}
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => setShowBalance(!showBalance)}
                      className="text-primary-foreground hover:bg-primary-foreground/10"
                    >
                      {showBalance ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                    </Button>
                  </CardTitle>
                </div>
                <Wallet className="w-8 h-8 text-primary-foreground/70" />
              </div>
            </CardHeader>
          </Card>

          {/* Action Buttons */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Link to="/deposito">
              <Card className="hover:bg-card-hover transition-colors cursor-pointer">
                <CardContent className="flex items-center gap-3 p-6">
                  <div className="w-12 h-12 bg-success/10 rounded-full flex items-center justify-center">
                    <Plus className="w-6 h-6 text-success" />
                  </div>
                  <div>
                    <h3 className="font-semibold">Depositar</h3>
                    <p className="text-sm text-muted-foreground">Adicionar dinheiro</p>
                  </div>
                </CardContent>
              </Card>
            </Link>

            <Link to="/transferencia">
              <Card className="hover:bg-card-hover transition-colors cursor-pointer">
                <CardContent className="flex items-center gap-3 p-6">
                  <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                    <ArrowUpRight className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h3 className="font-semibold">Transferir</h3>
                    <p className="text-sm text-muted-foreground">Enviar dinheiro</p>
                  </div>
                </CardContent>
              </Card>
            </Link>

            <Link to="/historico">
              <Card className="hover:bg-card-hover transition-colors cursor-pointer">
                <CardContent className="flex items-center gap-3 p-6">
                  <div className="w-12 h-12 bg-muted/50 rounded-full flex items-center justify-center">
                    <History className="w-6 h-6 text-muted-foreground" />
                  </div>
                  <div>
                    <h3 className="font-semibold">Histórico</h3>
                    <p className="text-sm text-muted-foreground">Ver transações</p>
                  </div>
                </CardContent>
              </Card>
            </Link>
          </div>

          {/* Recent Transactions */}
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <div>
                  <CardTitle>Transações recentes</CardTitle>
                  <CardDescription>Suas últimas movimentações</CardDescription>
                </div>
                <Link to="/historico">
                  <Button variant="outline" size="sm">
                    Ver todas
                  </Button>
                </Link>
              </div>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                {loading ? (
                  <div className="text-center py-8">
                    <p className="text-muted-foreground">Carregando transações...</p>
                  </div>
                ) : recentTransactions.length === 0 ? (
                  <div className="text-center py-8">
                    <History className="w-12 h-12 mx-auto mb-4 text-muted-foreground" />
                    <p className="text-muted-foreground">Nenhuma transação encontrada</p>
                  </div>
                ) : (
                  recentTransactions.map((transaction) => {
                    const transactionType = getTransactionType(transaction.type);
                    return (
                      <div
                        key={transaction.id}
                        className="flex items-center justify-between p-4 rounded-lg border border-border hover:bg-card-hover transition-colors"
                      >
                        <div className="flex items-center gap-3">
                          <div className="w-10 h-10 bg-muted/50 rounded-full flex items-center justify-center">
                            {getTransactionIcon(transactionType)}
                          </div>
                          <div>
                            <p className="font-medium">{transaction.description || 'Transação'}</p>
                            <p className="text-sm text-muted-foreground">
                              {formatDate(transaction.created_at)}
                            </p>
                          </div>
                        </div>
                        <div className="text-right space-y-1">
                          <p className={`font-semibold ${
                            transactionType === 'deposit' || transactionType === 'transfer_in' ? 'text-success' : 'text-destructive'
                          }`}>
                            {(transactionType === 'deposit' || transactionType === 'transfer_in') ? '+' : '-'}{formatCurrency(Math.abs(transaction.amount))}
                          </p>
                          {getStatusBadge(transaction.status)}
                        </div>
                      </div>
                    );
                  })
                )}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;