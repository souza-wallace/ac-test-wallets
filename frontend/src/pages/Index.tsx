import { Link } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Wallet, ArrowRight, Shield, Zap, Users } from "lucide-react";

const Index = () => {
  return (
    <div className="min-h-screen bg-background">
      {/* Hero Section */}
      <div className="relative">
        <div className="absolute inset-0 bg-gradient-to-br from-primary/20 via-background to-background" />
        <div className="relative max-w-6xl mx-auto px-4 py-24">
          <div className="text-center space-y-8">
            <div className="inline-flex items-center justify-center w-20 h-20 bg-primary rounded-full mb-8">
              <Wallet className="w-10 h-10 text-primary-foreground" />
            </div>
            
            <div className="space-y-4">
              <h1 className="text-5xl font-bold tracking-tight">
                Sua carteira digital
                <span className="block text-primary">segura e simples</span>
              </h1>
              <p className="text-xl text-muted-foreground max-w-2xl mx-auto">
                Transfira, receba e gerencie seu dinheiro com facilidade. 
                Controle total das suas finanças na palma da sua mão.
              </p>
            </div>

            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link to="/cadastro">
                <Button size="lg" className="text-lg px-8">
                  Criar conta gratuita
                  <ArrowRight className="w-5 h-5 ml-2" />
                </Button>
              </Link>
              <Link to="/login">
                <Button variant="outline" size="lg" className="text-lg px-8">
                  Fazer login
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </div>

      {/* Features Section */}
      <div className="max-w-6xl mx-auto px-4 py-24">
        <div className="text-center mb-16">
          <h2 className="text-3xl font-bold mb-4">Tudo que você precisa</h2>
          <p className="text-lg text-muted-foreground">
            Recursos pensados para facilitar sua vida financeira
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <Card className="text-center hover:shadow-lg transition-shadow">
            <CardHeader>
              <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <Zap className="w-6 h-6 text-primary" />
              </div>
              <CardTitle>Transferências instantâneas</CardTitle>
              <CardDescription>
                Envie e receba dinheiro na hora, sem burocracias
              </CardDescription>
            </CardHeader>
          </Card>

          <Card className="text-center hover:shadow-lg transition-shadow">
            <CardHeader>
              <div className="w-12 h-12 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <Shield className="w-6 h-6 text-success" />
              </div>
              <CardTitle>100% seguro</CardTitle>
              <CardDescription>
                Suas transações protegidas com a mais alta tecnologia de segurança
              </CardDescription>
            </CardHeader>
          </Card>

          <Card className="text-center hover:shadow-lg transition-shadow">
            <CardHeader>
              <div className="w-12 h-12 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <Users className="w-6 h-6 text-warning" />
              </div>
              <CardTitle>Suporte dedicado</CardTitle>
              <CardDescription>
                Nossa equipe está sempre pronta para ajudar você
              </CardDescription>
            </CardHeader>
          </Card>
        </div>
      </div>

      {/* CTA Section */}
      <div className="bg-primary/5 border-t border-border">
        <div className="max-w-4xl mx-auto px-4 py-16 text-center">
          <h2 className="text-3xl font-bold mb-4">
            Pronto para começar?
          </h2>
          <p className="text-lg text-muted-foreground mb-8">
            Crie sua conta gratuita e comece a usar agora mesmo
          </p>
          <Link to="/cadastro">
            <Button size="lg" className="text-lg px-8">
              Criar conta agora
              <ArrowRight className="w-5 h-5 ml-2" />
            </Button>
          </Link>
        </div>
      </div>
    </div>
  );
};

export default Index;
