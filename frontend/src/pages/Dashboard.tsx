import { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { useToast } from "@/hooks/use-toast";
import { api, Transaction } from "../services/api";
import { 
  Wallet, 
  Plus, 
  Send, 
  History, 
  LogOut,
  ArrowUpRight,
  ArrowDownLeft,
  Eye,
  EyeOff
} from "lucide-react";

const Dashboard = () => {
  const [showBalance, setShowBalance] = useState(true);
  const [balance, setBalance] = useState(0);
  const [userName, setUserName] = useState("");
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
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const getTransactionIcon = (type: string) => {
    switch (type.toLowerCase()) {
      case "deposit":
        return <Plus className="w-4 h-4 text-green-600" />;
      case "transfer":
        return <ArrowUpRight className="w-4 h-4 text-red-600" />;
      case "receive":
        return <ArrowDownLeft className="w-4 h-4 text-green-600" />;
      default:
        return <History className="w-4 h-4" />;
    }
  };

  const getStatusBadge = (status: string) => {
    switch (status.toLowerCase()) {
      case 'completed':
        return <Badge variant="default">Concluída</Badge>;
      case 'pending':
        return <Badge variant="secondary">Pendente</Badge>;
      case 'reversed':
        return <Badge variant="destructive">Revertida</Badge>;
      default:
        return <Badge variant="outline">Desconhecido</Badge>;
    }
  };

  return (
    <div className="min-h-screen bg-background p-4">
      <div className="max-w-2xl mx-auto">
        {/* Header */}
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
              <Wallet className="w-5 h-5 text-primary-foreground" />
            </div>
            <div>
              <h1 className="font-semibold text-lg">Carteira Digital</h1>
              <p className="text-sm text-muted-foreground">
                Olá, {userName || 'Usuário'}
              </p>
            </div>
          </div>
          
          <Button variant="outline" size="sm" onClick={handleLogout}>
            <LogOut className="w-4 h-4 mr-2" />
            Sair
          </Button>
        </div>

        <div className="space-y-6">
          {/* Balance Card */}
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-muted-foreground text-sm">Saldo disponível</p>
                  <div className="flex items-center gap-3 mt-2">
                    <p className="text-3xl font-bold">
                      {showBalance ? formatCurrency(balance) : "••••••••"}
                    </p>
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => setShowBalance(!showBalance)}
                    >
                      {showBalance ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                    </Button>
                  </div>
                </div>
                <Wallet className="w-12 h-12 text-muted-foreground" />
              </div>
            </CardContent>
          </Card>

          {/* Quick Actions */}
          <div className="grid grid-cols-3 gap-4">
            <Link to="/deposito">
              <Card className="hover:shadow-md transition-shadow cursor-pointer">
                <CardContent className="p-4 text-center">
                  <div className="w-12 h-12 bg-primary rounded-full flex items-center justify-center mx-auto mb-3">
                    <Plus className="w-6 h-6 text-primary-foreground" />
                  </div>
                  <h3 className="font-medium text-sm">Depositar</h3>
                </CardContent>
              </Card>
            </Link>

            <Link to="/transferencia">
              <Card className="hover:shadow-md transition-shadow cursor-pointer">
                <CardContent className="p-4 text-center">
                  <div className="w-12 h-12 bg-primary rounded-full flex items-center justify-center mx-auto mb-3">
                    <Send className="w-6 h-6 text-primary-foreground" />
                  </div>
                  <h3 className="font-medium text-sm">Transferir</h3>
                </CardContent>
              </Card>
            </Link>

            <Link to="/historico">
              <Card className="hover:shadow-md transition-shadow cursor-pointer">
                <CardContent className="p-4 text-center">
                  <div className="w-12 h-12 bg-primary rounded-full flex items-center justify-center mx-auto mb-3">
                    <History className="w-6 h-6 text-primary-foreground" />
                  </div>
                  <h3 className="font-medium text-sm">Histórico</h3>
                </CardContent>
              </Card>
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;