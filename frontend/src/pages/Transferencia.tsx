import { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { useToast } from "@/hooks/use-toast";
import { api } from "@/services/api";
import { ArrowLeft, ArrowUpRight, Wallet, User, DollarSign } from "lucide-react";

const Transferencia = () => {
  const [formData, setFormData] = useState({
    recipientEmail: "",
    amount: "",
    description: "",
  });
  const [isLoading, setIsLoading] = useState(false);
  const [currentBalance, setCurrentBalance] = useState(0);
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();
  const navigate = useNavigate();

  useEffect(() => {
    loadUserBalance();
  }, []);

  const loadUserBalance = async () => {
    try {
      const response = await api.getUserProfile();
      if (response.error) {
        toast({
          title: "Erro ao carregar saldo",
          description: response.error,
          variant: "destructive",
        });
      } else if (response.data) {
        setCurrentBalance(response.data.wallet?.balance || 0);
      }
    } catch (error) {
      toast({
        title: "Erro de conexão",
        description: "Não foi possível carregar o saldo",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (field: string, value: string) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(value);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    const amount = parseFloat(formData.amount);
    
    if (amount <= 0) {
      toast({
        title: "Valor inválido",
        description: "O valor deve ser maior que zero.",
        variant: "destructive",
      });
      return;
    }

    if (amount > currentBalance) {
      toast({
        title: "Saldo insuficiente",
        description: "Você não tem saldo suficiente para esta transferência.",
        variant: "destructive",
      });
      return;
    }

    setIsLoading(true);

    try {
      // Aqui você precisaria buscar o ID do usuário pelo email
      // Por simplicidade, vou usar um ID fixo (2) para teste
      const toUserId = 2; // Em produção, buscar pelo email
      
      const response = await api.transfer(formData.recipientEmail, amount, formData.description);
      
      if (response.error) {
        toast({
          title: "Erro na transferência",
          description: response.error || response.details,
          variant: "destructive",
        });
      } else {
        toast({
          title: "Transferência realizada!",
          description: `${formatCurrency(amount)} enviado com sucesso`,
        });
        
        // Reset form e atualiza saldo
        setFormData({
          recipientEmail: "",
          amount: "",
          description: "",
        });
        
        // Recarrega o saldo
        loadUserBalance();
        
        // Redireciona para dashboard após 2 segundos
        setTimeout(() => {
          navigate('/dashboard');
        }, 2000);
      }
    } catch (error) {
      toast({
        title: "Erro de conexão",
        description: "Não foi possível processar a transferência",
        variant: "destructive",
      });
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <header className="border-b border-border bg-card">
        <div className="max-w-4xl mx-auto px-4 py-4 flex items-center gap-4">
          <Link to="/dashboard">
            <Button variant="outline" size="sm">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Voltar
            </Button>
          </Link>
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
              <ArrowUpRight className="w-5 h-5 text-primary-foreground" />
            </div>
            <div>
              <h1 className="font-semibold text-lg">Transferir dinheiro</h1>
              <p className="text-sm text-muted-foreground">Envie dinheiro para outros usuários</p>
            </div>
          </div>
        </div>
      </header>

      <div className="max-w-2xl mx-auto px-4 py-8">
        <div className="grid gap-6">
          {/* Balance Card */}
          <Card className="bg-gradient-to-r from-primary to-primary-hover text-primary-foreground">
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-primary-foreground/70 text-sm">Saldo disponível</p>
                  <p className="text-2xl font-bold">
                    {loading ? "Carregando..." : formatCurrency(currentBalance)}
                  </p>
                </div>
                <Wallet className="w-8 h-8 text-primary-foreground/70" />
              </div>
            </CardContent>
          </Card>

          {/* Transfer Form */}
          <Card>
            <CardHeader>
              <CardTitle>Dados da transferência</CardTitle>
              <CardDescription>
                Preencha os dados para enviar dinheiro
              </CardDescription>
            </CardHeader>
            <CardContent>
              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="space-y-2">
                  <Label htmlFor="recipientEmail">E-mail do destinatário</Label>
                  <div className="relative">
                    <User className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <Input
                      id="recipientEmail"
                      type="email"
                      placeholder="destinatario@email.com"
                      value={formData.recipientEmail}
                      onChange={(e) => handleInputChange("recipientEmail", e.target.value)}
                      className="pl-10"
                      required
                    />
                  </div>
                  <p className="text-xs text-muted-foreground">
                    Digite o e-mail da pessoa que receberá o dinheiro
                  </p>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="amount">Valor (R$)</Label>
                  <div className="relative">
                    <DollarSign className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                    <Input
                      id="amount"
                      type="number"
                      step="0.01"
                      min="0.01"
                      max={currentBalance}
                      placeholder="0,00"
                      value={formData.amount}
                      onChange={(e) => handleInputChange("amount", e.target.value)}
                      className="pl-10"
                      required
                    />
                  </div>
                  <p className="text-xs text-muted-foreground">
                    Valor máximo: {formatCurrency(currentBalance)}
                  </p>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="description">Descrição (opcional)</Label>
                  <Input
                    id="description"
                    type="text"
                    placeholder="Ex: Pagamento do almoço"
                    value={formData.description}
                    onChange={(e) => handleInputChange("description", e.target.value)}
                    maxLength={100}
                  />
                  <p className="text-xs text-muted-foreground">
                    Adicione uma descrição para identificar esta transferência
                  </p>
                </div>

                {/* Preview */}
                {formData.amount && parseFloat(formData.amount) > 0 && (
                  <Card className="bg-muted/50">
                    <CardContent className="p-4">
                      <h3 className="font-semibold mb-2">Resumo da transferência</h3>
                      <div className="space-y-1 text-sm">
                        <div className="flex justify-between">
                          <span>Destinatário:</span>
                          <span>{formData.recipientEmail || "—"}</span>
                        </div>
                        <div className="flex justify-between">
                          <span>Valor:</span>
                          <span className="font-semibold text-destructive">
                            -{formatCurrency(parseFloat(formData.amount))}
                          </span>
                        </div>
                        <div className="flex justify-between">
                          <span>Descrição:</span>
                          <span>{formData.description || "Sem descrição"}</span>
                        </div>
                        <hr className="my-2" />
                        <div className="flex justify-between font-semibold">
                          <span>Saldo após transferência:</span>
                          <span>{formatCurrency(currentBalance - parseFloat(formData.amount))}</span>
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                )}

                <div className="flex gap-3">
                  <Link to="/dashboard" className="flex-1">
                    <Button variant="outline" className="w-full">
                      Cancelar
                    </Button>
                  </Link>
                  <Button type="submit" className="flex-1" disabled={isLoading}>
                    {isLoading ? "Processando..." : "Transferir"}
                  </Button>
                </div>
              </form>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default Transferencia;